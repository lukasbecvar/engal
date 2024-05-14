import axios from 'axios'
import { useNavigate } from "react-router-dom";
import React, { useEffect, useState } from 'react' 

// engal component
import LoadingComponent from './sub-component/LoadingComponent'
import ErrorMessageComponent from './error/ErrorMessageComponent'
import BreadcrumbComponent from './navigation/BreadcrumbComponent'
import NavigationComponent from './navigation/NavigationComponent'

// engal utils
import { DEV_MODE } from '../config'

/*
 * Media (images/videos) upload form component
*/
export default function UploadComponent() {
    // get storage data
    let apiUrl = localStorage.getItem('api-url')
    let loginToken = localStorage.getItem('login-token')

    const navigate = useNavigate();

    // upload file list
    const [files, setFiles] = useState([])

    // upload state
    const [loading, setLoading] = useState(true)
    const [apiError, setApiError] = useState(null)
    const [error, setError] = useState(null)
    const [status, setStatus] = useState(null)
    const [progress, setProgress] = useState(0)
    const [uploadPolicy, setUploadPolicy] = useState([null])

    // gallery select data
    const [galleryNames, setGalleryNames] = useState([])
    const [selectedGallery, setSelectedGallery] = useState('')
    const [galleryNameInputVisible, setGalleryNameInputVisible] = useState(false)
    const [newGalleryName, setNewGalleryName] = useState('')

    // fetch user gallery list
    useEffect(() => {
        async function fetchGalleryNames() {
            try {
                const response = await axios.get(apiUrl + '/api/gallery/list', {
                    headers: {
                        'Authorization': `Bearer ${loginToken}`
                    },
                })
                setGalleryNames(response.data.gallery_list)
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
    }, [apiUrl, loginToken])

    // get backend upload policy config
    useEffect(() => {
        async function getPolicy() {
            setLoading(true)
            try {
                const response = await axios.get(apiUrl + '/api/upload/config/policy', {
                    headers: {
                        'Authorization': `Bearer ${loginToken}`
                    },
                })
                setUploadPolicy(response.data.policy)
            } catch (error) {
                if (DEV_MODE) {
                    console.log('Error to get upload policy: ' + error)
                }
                setApiError('Error to get upload policy')
            } finally {
                setLoading(false)
            }
        }
        getPolicy()
    }, [apiUrl, loginToken])

    // calculate max files size
    const MAX_FILE_LIST_SIZE_BYTES = uploadPolicy.MAX_FILES_SIZE * 1024 * 1024 * 1024

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
        const fileList = e.target.files
    
        // file extension check
        for (let i = 0; i < fileList.length; i++) {
            const file_extension = fileList[i].name.split('.').pop().toLowerCase()
            if (!uploadPolicy.ALLOWED_FILE_EXTENSIONS.includes(file_extension)) {
                setError(`File ${fileList[i].name} has an invalid extension.`)
                return
            }
        }
    
        // file count check
        if (files.length + fileList.length > uploadPolicy.MAX_FILES_COUNT) {
            setError(`Maximum number of allowable file uploads (${uploadPolicy.MAX_FILES_COUNT}) has been exceeded.`)
            return
        }
    
        setFiles([...files, ...fileList])
    }
    
    // handle remove file from list
    const handleRemoveFile = (index) => {
        const updated_files = files.filter((_, i) => i !== index)
        setFiles(updated_files)
    }

    // drag & drop handlers
    const handleFileDrop = (e) => {
        e.preventDefault()
        const fileList = e.dataTransfer.files
        handleFileChange({ target: { files: fileList } })
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
        if ((newGalleryName == '' && selectedGallery == '') || (selectedGallery == 'new name' && newGalleryName == '')) {
            setError('Please select gallery name')
            return
        }

        // get file list size
        const totalSizeBytes = files.reduce((total, file) => total + file.size, 0)
    
        // check file list size
        if (totalSizeBytes > MAX_FILE_LIST_SIZE_BYTES) {
            setError(`Maximum file list size (${uploadPolicy.MAX_FILES_SIZE} Gb) has been exceeded.`)
            return
        }
    
        // init form files
        const formData = new FormData()
        files.forEach((file) => formData.append('files[]', file))

        // append gallery name to request
        if (newGalleryName != '') {
            formData.append('gallery_name', newGalleryName)
        } else {
            formData.append('gallery_name', selectedGallery)
        }
    
        // main upload process
        try {
            const response = await axios.post(apiUrl + '/api/upload', formData, {
                headers: {
                    'Authorization': `Bearer ${loginToken}`
                },
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
                    setStatus('Uploading files ' + percentCompleted + '%')
                    setProgress(percentCompleted)
                    if (percentCompleted == 100) {
                        setStatus('File processing...')
                    }
                },
            })
            if (response.data.message == 'files uploaded successfully') {
                setStatus('File upload completed!')
                navigate("/");
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
    if (apiError != null) {
        return <ErrorMessageComponent message={apiError}/>
    }

    return (
        <div>
            <NavigationComponent/>            
            <BreadcrumbComponent/>
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
                    {galleryNameInputVisible == false &&
                        <select className="gallery-select" onChange={handleNameChange} value={selectedGallery}>
                            <option value="">Select Gallery</option>
                            {galleryNames.map((gallery, index) => (
                                <option key={index} value={gallery.name}>{gallery.name}</option>
                            ))}
                            <option value="new name">New Name</option>
                        </select>
                    }
            
                    {/* new gallery name input */}
                    {galleryNameInputVisible && (
                        <input 
                            className="new-gallery-input" 
                            type="text" 
                            value={newGalleryName} 
                            onChange={(e) => setNewGalleryName(e.target.value)} 
                            placeholder="Enter new gallery name" 
                            maxLength={uploadPolicy.MAX_GALLERY_NAME_LENGTH}
                        />
                    )}
            
                    {/* submit button */}
                    <button className="upload-button" onClick={handleSubmit}>Upload</button>
                </div>
            </div>
        </div>
    )
}