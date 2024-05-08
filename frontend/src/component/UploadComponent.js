import axios from 'axios'
import React, { useEffect, useState } from 'react' 

// engal component
import LoadingComponent from './sub-component/LoadingComponent'
import ErrorMessageComponent from './sub-component/error/ErrorMessageComponent'
import UserNavigationComponent from './sub-component/navigation/UserNavigationComponent'
import MainNavigationComponent from './sub-component/navigation/MainNavigationComponent'

// engal utils
import { DEV_MODE } from '../config'

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
    const [gallery_names, setGalleryNames] = useState([]);
    const [selected_gallery, setSelectedGallery] = useState('');
    const [gallery_name_input_visible, setGalleryNameInputVisible] = useState(false);
    const [new_gallery_name, setNewGalleryName] = useState('');

    // fetch user gallery list
    useEffect(() => {
        async function fetchGalleryNames() {
            try {
                const response = await axios.get(api_url + '/api/gallery/list', {
                    headers: {
                        'Authorization': `Bearer ${login_token}`
                    },
                });
                setGalleryNames(response.data.gallery_names);
            } catch (error) {
                if (DEV_MODE) {
                    console.log('Error fetching gallery names: ' + error);
                }
                setApiError('Error to get gallery names')
            } finally {
                setLoading(false)
            }
        }
        fetchGalleryNames();
    }, [api_url, login_token]);

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
        setSelectedGallery(event.target.value);
        if (event.target.value === "new name") {
            setGalleryNameInputVisible(true);
        } else {
            setGalleryNameInputVisible(false);
            setNewGalleryName('')
        }
    }

    // handle change in files list
    const handleFileChange = (e) => {
        const file_list = e.target.files;
    
        // file extension check
        for (let i = 0; i < file_list.length; i++) {
            const file_extension = file_list[i].name.split('.').pop().toLowerCase();
            if (!upload_policy.ALLOWED_FILE_EXTENSIONS.includes(file_extension)) {
                setError(`File ${file_list[i].name} has an invalid extension.`);
                return;
            }
        }
    
        // file count check
        if (files.length + file_list.length > upload_policy.MAX_FILES_COUNT) {
            setError(`Maximum number of allowable file uploads (${upload_policy.MAX_FILES_COUNT}) has been exceeded.`);
            return;
        }
    
        setFiles([...files, ...file_list]);
    };
    
    // handle remove file from list
    const handleRemoveFile = (index) => {
        const updated_files = files.filter((_, i) => i !== index)
        setFiles(updated_files)
    }

    // handle upload submit
    const handleSubmit = async () => {
        // reset error
        setError(null)

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
            <div className="app-component">
                
                {error && <p>{error}</p>}
                {status && <p>{status}</p>}

                <progress value={progress} max="100" />
                <input type="file" multiple onChange={handleFileChange} />

                <select onChange={handleNameChange} value={selected_gallery}>
                    <option value="">Select Gallery</option>
                    {gallery_names.map((name, index) => (
                        <option key={index} value={name}>{name}</option>
                    ))}
                    <option value="new name">New Name</option>
                </select>
                {gallery_name_input_visible && (
                    <input 
                        type="text" 
                        value={new_gallery_name} 
                        onChange={(e) => setNewGalleryName(e.target.value)} 
                        placeholder="Enter new gallery name" 
                        maxLength={upload_policy.MAX_GALLERY_NAME_LENGTH}
                    />
                )}
               
                <button onClick={handleSubmit}>Upload</button>
                <div>
                    {files.map((file, index) => (
                        <div key={index}>
                            <span>{file.name}</span>
                            <button onClick={() => handleRemoveFile(index)}>Remove</button>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    )
}