import { useEffect, useState } from 'react';

// import config values
import { DEV_MODE } from '../config';

// import engal utils
import { appReload } from '../utils/AppUtils';
import { getApiUrl } from '../utils/ApiUtils';
import { getUserToken } from '../utils/AuthUtils';

// import engal components
import LoadingComponent from './sub-components/LoadingComponent';
import ErrorBoxComponent from './sub-components/alerts/ErrorBoxComponent';
import SuccessMessageBox from './sub-components/alerts/SuccessMessageBox';
import WarningMessageBox from './sub-components/alerts/WarningMessageBox';

export default function UploaderComponent() 
{
    // update app title
    document.title = 'Engal: upload';

    // retrieve API URL from local storage
    let api_url = getApiUrl();

    // get current user token
    let user_token = getUserToken();

    // state variable for managing component state
    const [loading, setLoading] = useState(true);
    const [error_msg, setErrorMsg] = useState(null);
    const [percentage, setPercentage] = useState(0);
    const [success_message, setSuccessMsg] = useState(null);
    const [warning_message, setWarningMsg] = useState(null);

    // form data
    const [images, setImages] = useState([]);
    const [gallery_options, setGalleryOptions] = useState([]);
    const [new_gallery_name, setNewGalleryName] = useState('');
    const [selected_gallery, setSelectedGallery] = useState(null);

    // handle gallery name change
    function handleGalleryChange(event) {
        setSelectedGallery(event.target.value);
    };

    // handle new gallery name change
    function handleNewGalleryNameChange(event) {
        setNewGalleryName(event.target.value);
    };

    // handle image input change
    function handleImageChange(event) {
        setImages(Array.from(event.target.files));
    };

    // main upload function
    async function handleUpload() {

        // reset messages
        setErrorMsg(null);
        setSuccessMsg(null);
        setWarningMsg(null);

        // get gallery name
        let gallery_name = selected_gallery === 'New gallery' ? new_gallery_name : selected_gallery;

        // set new gallery name
        if (gallery_options.length <= 1) {
            gallery_name = new_gallery_name;
        }

        // check gallery name length reached
        if (gallery_name.length >= 31) {
            setErrorMsg('maximal gallery name length is 30 characters');

        // check gallery name minimal length
        } else if (gallery_name.length <= 3) {
            setErrorMsg('minimal gallery name length is 4 characters');

        // check if file input is not empty
        } else if (images.length < 1) {
            setErrorMsg('your file input is empty');
        } else {

            // main upload process
            try {
                let uploaded = 0;
        
                // calculate total files for progress calculation
                const totalFiles = images.length;

                // set minimal progress
                setPercentage(1);
            
                // upload all images loop
                for (const image of images) {

                    // set request data
                    const formData = new FormData();
                    formData.append('token', user_token);
                    formData.append('gallery', gallery_name);
                    formData.append('image', image);
            
                    // make request with data
                    const response = await fetch(api_url + '/media/upload', {
                        method: 'POST',
                        body: formData
                    });
            
                    // get response
                    const result = await response.json();
            
                    // check if status is success
                    if (result.status === 'success') {
                        setWarningMsg(image.name + ': ' + result.message);
                    } else {

                        // check if gallry name is empty
                        if (result.message === 'required post data: gallery') {
                            setErrorMsg('your gallery name is empty');
                        } else {

                            // set other errors
                            setErrorMsg(result.message);
                        }
                    }
            
                    // increase uploaded files count 
                    uploaded++;
            
                    // calculate and set the percentage
                    const currentPercentage = Math.round((uploaded / totalFiles) * 100);
                    setPercentage(currentPercentage);
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.error('error during upload: ', error);
                }
                setErrorMsg('unknown upload error, please contact your administrator');
            } finally {

                // reset default upload sate values
                setWarningMsg(null); 
                setPercentage(0);
                
                // set success message
                setSuccessMsg('upload process is success');
            }
        }
    }
    
    useEffect(() => {
        // reload app (show main component [list]) only if upload success
        if (success_message !== null && error_msg === null) {
            appReload();
        }   
    })

    useEffect(() => {

        // get gallery list from gallery name selection
        const fetchGalleryList = async () => {
            try {
                const formData = new FormData();
    
                // set post data
                formData.append('token', user_token);

                // send request
                const response = await fetch(api_url + '/gallery/list', {
                    method: 'POST',
                    body: formData
                });

                // get response
                const result = await response.json();

                // check response
                if (result.status === 'success') {
                    const galleryList = result.gallery_list.map((gallery) => gallery.name);
                    setGalleryOptions([...galleryList, 'New gallery']);
                    setSelectedGallery(galleryList[0]); 
                } else {
                    setErrorMsg('error fetching gallery list');
                    if (DEV_MODE) {
                        console.error('error fetching gallery list: ', result.message);
                    }
                }
            } catch (error) {
                setErrorMsg('error fetching gallery list');
                if (DEV_MODE) {
                    console.error('error fetching gallery list: ', error);
                }
            } finally {
                setLoading(false);
            }
        };

        fetchGalleryList();
    }, [api_url, user_token]);

    // show loading
    if (loading === true) {
        return (<LoadingComponent/>);
    } else {
        return (
            <div className='component'>
                <div className='container mt-5 mb-5'>
                    <div className='w-4/5 m-auto text-center'>
                        <div className='mask d-flex align-items-center h-100 gradient-custom-3'>
                            <div className='container h-100'>
                                <div className='row d-flex justify-content-center align-items-center h-100'>
                                    <div className='col-12 col-md-9 col-lg-7 col-xl-6'>
                                        <div className='card bg-dark'>
                                            <div className='card-body p-5 text-light'>
                                                <h2 className='text-uppercase text-center mb-3 text-light'>Image upload</h2>
                                                
                                                {/* upload progress bar */}
                                                {percentage !== 0 && (
                                                    <div className="progress mb-3">
                                                        <div
                                                            className="progress-bar bg-success"
                                                            role="progressbar"
                                                            style={{ width: `${percentage}%` }}
                                                            aria-valuenow={percentage}
                                                            aria-valuemin="0"
                                                            aria-valuemax="100">
                                                            {percentage}%
                                                        </div>
                                                    </div>
                                                )}

                                                {/* warning message box alert */}
                                                {warning_message !== null && (
                                                    <WarningMessageBox warning_message={warning_message}/>
                                                )}

                                                {/* error message box alert */}
                                                {error_msg !== null && (
                                                    <ErrorBoxComponent error_msg={error_msg}/>
                                                )}

                                                {/* success message box alert */}
                                                {success_message !== null && error_msg === null && (
                                                    <SuccessMessageBox success_message={success_message}/>
                                                )}
    
                                                <div className='upload-form'>
                                                    
                                                    {/* files input */}
                                                    <label htmlFor='images' className='form-label'>Image(s)</label>
                                                    <input type='file' id='images' name='images[]' className='form-control mb-3' multiple accept='image/*' onChange={handleImageChange}/>
    
                                                    {/* gallery name selection */}
                                                    <span>
                                                        <label htmlFor='galleryName' className='form-label'>Gallery Name</label>
                                                        <select id='galleryName' name='galleryName' className='form-control form-control-lg mb-3' onChange={handleGalleryChange}>
                                                            {gallery_options.map((option) => (
                                                                <option key={option} value={option}>{option}</option>
                                                            ))}
                                                        </select>
                                                    </span>
                                                
                                                    {/* show new gallery input (if selected) */}
                                                    {selected_gallery === 'New gallery' && (
                                                        <div>
                                                            <label htmlFor='newGalleryName' className='form-label'>New Gallery Name</label>
                                                            <input type='text' id='newGalleryName' name='newGalleryName' className='form-control form-control-lg mb-3' placeholder='New Gallery name' onChange={handleNewGalleryNameChange}/>
                                                        </div>
                                                    )}
    
                                                    {/* show new gallery input (if list empty) */}
                                                    {gallery_options.length <= 1 && (
                                                        <div>
                                                            <label htmlFor='newGalleryName' className='form-label'>New Gallery Name</label>
                                                            <input type='text' id='newGalleryName' name='newGalleryName' className='form-control form-control-lg mb-3' placeholder='New Gallery name' maxLength={30} onChange={handleNewGalleryNameChange}/>
                                                        </div>
                                                    )}
    
                                                    {/* upload submit button */}
                                                    <div className='m-3 justify-content-center'>
                                                        <button type='button' className='btn btn-success btn-block btn-lg gradient-custom-4 text-light' onClick={handleUpload}>Upload</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}
