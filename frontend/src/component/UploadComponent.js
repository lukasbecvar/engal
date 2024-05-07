import React, { useState } from 'react' 
import axios from 'axios'
import UserNavigationComponent from './sub-component/navigation/UserNavigationComponent'
import MainNavigationComponent from './sub-component/navigation/MainNavigationComponent'

export default function UploadComponent() {
    const MAX_FILES = 2000
    const MAX_FILE_LIST_SIZE_BYTES = 8 * 1024 * 1024 * 1024 // 20 GB

    let api_url = localStorage.getItem('api-url')
    let login_token = localStorage.getItem('login-token')

    const [status, setStatus] = useState(null)
    const [files, setFiles] = useState([])
    const [progress, setProgress] = useState(0)

    const allowedFileExtensions = [
        'jpeg', 
        'jpg', 
        'png', 
        'gif',
        
        'qt', 
        'mp4', 
        'm4p', 
        'm4v', 
        'amv', 
        'wmv',
        'mov', 
        'flv', 
        'm4v', 
        'mkv', 
        '3gp', 
        '3g2', 
        'avi', 
        'mpg', 
        'MP2T', 
        'webm', 
        'mpeg', 
        'x-m4v',
        'x-ms-wmv', 
        'quicktime'
    ]; 

    const handleFileChange = (e) => {
        const fileList = e.target.files;
    
        // file extension check
        for (let i = 0; i < fileList.length; i++) {
            const fileExtension = fileList[i].name.split('.').pop().toLowerCase();
            if (!allowedFileExtensions.includes(fileExtension)) {
                alert(`File ${fileList[i].name} has an invalid extension.`);
                return;
            }
        }
    
        // file count check
        if (files.length + fileList.length > MAX_FILES) {
            alert(`Maximum number of allowable file uploads (${MAX_FILES}) has been exceeded.`);
            return;
        }
    
        setFiles([...files, ...fileList]);
    };
    

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
                    setStatus('Uploading files ' + percentCompleted + '%')
                    setProgress(percentCompleted)
                    if (percentCompleted == 100) {
                        setStatus('File processing...')
                    }
                },
            })
            if (response.data.message == 'Files uploaded successfully') {
                setStatus('File upload completed!')
            }
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
                {status && <p>{status}</p>}
                <progress value={progress} max="100" />
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
            </div>
        </div>
    )
}