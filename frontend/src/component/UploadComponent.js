import axios from 'axios'
import React, { useEffect, useState } from 'react' 

// engal component
import LoadingComponent from './sub-component/LoadingComponent'
import ErrorMessageComponent from './sub-component/error/ErrorMessageComponent'
import UserNavigationComponent from './sub-component/navigation/UserNavigationComponent'
import MainNavigationComponent from './sub-component/navigation/MainNavigationComponent'

// engal utils
import { DEV_MODE } from '../config'

/*
 * Media (images/videos) upload form component
*/
export default function UploadComponent() {
    // get storage data
    let api_url = localStorage.getItem('api-url')
    let login_token = localStorage.getItem('login-token')

    // upload file list
    const [files, setFiles] = useState([])

    // upload state
    const [loading, setLoading] = useState(true)
    const [api_error, setApiError] = useState(null)
    const [error, setError] = useState(null)
    const [status, setStatus] = useState(null)
    const [progress, setProgress] = useState(0)
    const [upload_policy, setUploadPolicy] = useState([null])

    // gallery select data
    const [gallery_names, setGalleryNames] = useState([])
    const [selected_gallery, setSelectedGallery] = useState('')
    const [gallery_name_input_visible, setGalleryNameInputVisible] = useState(false)
    const [new_gallery_name, setNewGalleryName] = useState('')

    // fetch user gallery list
    useEffect(() => {
        async function fetchGalleryNames() {
            try {
                const response = await axios.get(api_url + '/api/gallery/list', {
                    headers: {
                        'Authorization': `Bearer ${login_token}`
                    },
                })
                setGalleryNames(response.data.gallery_names)
            } catch (error) {
                if (DEV_MODE) {
                    console.log('Error fetching gallery names: ' + error)
                }
                setApiError('Error to get gallery names')
            } finally {
                setLoading(false)
            }
        }
        fetchGalleryNames()
    }, [api_url, login_token])

    // get backend upload policy config
    useEffect(() => {
        async function getPolicy() {
            setLoading(true)
            try {
                const response = await axios.get(api_url + '/api/upload/config/policy', {
                    headers: {
                        'Authorization': `Bearer ${login_token}`
                    },
                })
                setUploadPolicy(response.data)
            } catch (error) {
                if (DEV_MODE) {
                    console.log('error to get upload policy: ' + error)
                }
                setApiError('Error to get upload policy')
            } finally {
                setLoading(false)
            }
        }
        getPolicy()
    }, [api_url, login_token])

    // calculate max files size
    const MAX_FILE_LIST_SIZE_BYTES = upload_policy.MAX_FILES_SIZE * 1024 * 1024 * 1024

    // handle gallery name change
    const handleNameChange = (event) => {
        setSelectedGallery(event.target.value)
        if (event.target.value === "new name") {
            setGalleryNameInputVisible(true)
        } else {
            setGalleryNameInputVisible(false)
            setNewGalleryName('')
        }
    }

    // handle change in files list
    const handleFileChange = (e) => {
        const file_list = e.target.files
    
        // file extension check
        for (let i = 0; i < file_list.length; i++) {
            const file_extension = file_list[i].name.split('.').pop().toLowerCase()
            if (!upload_policy.ALLOWED_FILE_EXTENSIONS.includes(file_extension)) {
                setError(`File ${file_list[i].name} has an invalid extension.`)
                return
            }
        }
    
        // file count check
        if (files.length + file_list.length > upload_policy.MAX_FILES_COUNT) {
            setError(`Maximum number of allowable file uploads (${upload_policy.MAX_FILES_COUNT}) has been exceeded.`)
            return
        }
    
        setFiles([...files, ...file_list])
    }
    
    // handle remove file from list
    const handleRemoveFile = (index) => {
        const updated_files = files.filter((_, i) => i !== index)
        setFiles(updated_files)
    }

    // drag & drop handlers
    const handleFileDrop = (e) => {
        e.preventDefault()
        const file_list = e.dataTransfer.files
        handleFileChange({ target: { files: file_list } })
    }
    const handleDragOver = (e) => {
        e.preventDefault()
    }
    
    // handle upload submit
    const handleSubmit = async () => {
        // state reset
        setError(null)
        setStatus(null)

        // check file list set
        if (files.length < 1) {
            setError('Please add input files')
            return
        }

        // check if gallery name seted
        if ((new_gallery_name == '' && selected_gallery == '') || (selected_gallery == 'new name' && new_gallery_name == '')) {
            setError('Please select gallery name')
            return
        }

        // get file list size
        const totalSizeBytes = files.reduce((total, file) => total + file.size, 0)
    
        // check file list size
        if (totalSizeBytes > MAX_FILE_LIST_SIZE_BYTES) {
            setError(`Maximum file list size (${upload_policy.MAX_FILES_SIZE} Gb) has been exceeded.`)
            return
        }
    
        // init form files
        const formData = new FormData()
        files.forEach((file) => formData.append('files[]', file))

        // append gallery name to request
        if (new_gallery_name != '') {
            formData.append('gallery_name', new_gallery_name)
        } else {
            formData.append('gallery_name', selected_gallery)
        }
    
        // main upload process
        try {
            const response = await axios.post(api_url + '/api/upload', formData, {
                headers: {
                    'Authorization': `Bearer ${login_token}`
                },
                onUploadProgress: (progress_event) => {
                    const percent_completed = Math.round((progress_event.loaded * 100) / progress_event.total)
                    setStatus('Uploading files ' + percent_completed + '%')
                    setProgress(percent_completed)
                    if (percent_completed == 100) {
                        setStatus('File processing...')
                    }
                },
            })
            if (response.data.message == 'files uploaded successfully') {
                setStatus('File upload completed!')
            }
        } catch (error) {
            if (DEV_MODE) {
                console.log('Upload failed: ' + error)
            }
            setError('Unknown upload error')
        }
    }

    // show loading component
    if (loading) {
        return <LoadingComponent/>
    }

    // show error message component
    if (api_error != null) {
        return <ErrorMessageComponent message={api_error}/>
    }

    return (
        <div>
            <MainNavigationComponent/>            
            <UserNavigationComponent/>
            <div className="app-component upload-component">
                
                <div className="upload-form-container">
                    <h2 className="upload-title">Media Upload</h2>
            
                    {/* error message */}
                    {error && <p className="error-message">{error}</p>}
            
                    {/* status message */}
                    {status && <p className="status-message">{status}</p>}
            
                    {/* progress bar */}
                    {progress > 0 && 
                        <progress className="progress-bar" value={progress} max="100" />
                    }
        
                    {/* file input */}
                    <div className="file-input-box">
                        {/* file list container */}
                        {files.length >= 1 && 
                            <div className="file-list-container">
                                <span className="file-list-title"><p>File list</p></span>
                                {/* file items */}
                                {files.map((file, index) => (
                                    <div key={index} className="file-item">
                                        <span className="file-name">{file.name}</span>
                                        <button className="remove-button" onClick={() => handleRemoveFile(index)}>Remove</button>
                                    </div>
                                ))}
                            </div>
                        }
                        <div className="file-input" onDrop={handleFileDrop} onDragOver={handleDragOver} onClick={() => document.querySelector('.browser-file-input').click()}>
                            <p>Drag & drop or click files here</p>
                        </div>
                        {/* button for selecting files through browser */}
                        <input className="browser-file-input display-none" type="file" multiple onChange={handleFileChange} />
                    </div>
            
                    {/* gallery select */}
                    {gallery_name_input_visible == false &&
                        <select className="gallery-select" onChange={handleNameChange} value={selected_gallery}>
                            <option value="">Select Gallery</option>
                            {gallery_names.map((name, index) => (
                                <option key={index} value={name}>{name}</option>
                            ))}
                            <option value="new name">New Name</option>
                        </select>
                    }
            
                    {/* new gallery name input */}
                    {gallery_name_input_visible && (
                        <input 
                            className="new-gallery-input" 
                            type="text" 
                            value={new_gallery_name} 
                            onChange={(e) => setNewGalleryName(e.target.value)} 
                            placeholder="Enter new gallery name" 
                            maxLength={upload_policy.MAX_GALLERY_NAME_LENGTH}
                        />
                    )}
            
                    {/* submit button */}
                    <button className="upload-button" onClick={handleSubmit}>Upload</button>
                </div>
            </div>
        </div>
    )
}
