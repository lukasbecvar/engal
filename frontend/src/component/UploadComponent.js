import React, { useState } from 'react'
import axios from 'axios'
import UserNavigationComponent from './sub-component/navigation/UserNavigationComponent'
import MainNavigationComponent from './sub-component/navigation/MainNavigationComponent'

export default function UploadComponent() {
    const MAX_FILES = 2000
    const MAX_FILE_LIST_SIZE_BYTES = 20 * 1024 * 1024 * 1024 // 20 GB

    let api_url = localStorage.getItem('api-url')
    let login_token = localStorage.getItem('login-token')

    const [files, setFiles] = useState([])
    const [progress, setProgress] = useState(0)

    const handleFileChange = (e) => {
        const fileList = e.target.files
    
        // file count limit check
        if (files.length + fileList.length > MAX_FILES) {
            alert(`Maximum number of allowable file uploads (${MAX_FILES}) has been exceeded.`)
            return
        }
    
        setFiles([...files, ...fileList])
    }

    const handleRemoveFile = (index) => {
        const updatedFiles = files.filter((_, i) => i !== index)
        setFiles(updatedFiles)
    }

    const handleSubmit = async () => {
        // get file list size
        const totalSizeBytes = files.reduce((total, file) => total + file.size, 0)
    
        // check file list size
        if (totalSizeBytes > MAX_FILE_LIST_SIZE_BYTES) {
            alert(`Maximum file list size (${MAX_FILE_LIST_SIZE_BYTES} bytes) has been exceeded.`)
            return
        }
    
        const formData = new FormData()
        files.forEach((file) => formData.append('files[]', file))
    
        try {
            const response = await axios.post(api_url + '/api/upload', formData, {
                headers: {
                    'Authorization': `Bearer ${login_token}`
                },
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
                    setProgress(percentCompleted)
                },
            })
            console.log(response.data)
        } catch (error) {
            console.error('Upload failed', error)
        }
    }

    return (
        <div>
            <MainNavigationComponent/>            
            <UserNavigationComponent/>
            <div className="app-component">
                <input type="file" multiple onChange={handleFileChange} />
                <button onClick={handleSubmit}>Submit</button>
                <div>
                    {files.map((file, index) => (
                        <div key={index}>
                            <span>{file.name}</span>
                            <button onClick={() => handleRemoveFile(index)}>Remove</button>
                        </div>
                    ))}
                </div>
                <progress value={progress} max="100" />
            </div>
        </div>
    )
}
