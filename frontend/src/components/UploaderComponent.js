import { useState } from 'react';

// import engal utils
import { getUserToken } from '../utils/AuthUtils';

// import engal components
import ErrorBoxComponent from './sub-components/ErrorBoxComponent';

export default function UploaderComponent() {
    // retrieve API URL from local storage
    let api_url = localStorage.getItem('api-url');

    // get current user token
    let user_token = getUserToken();

    // state variables for managing component state
    const [error_msg, setErrorMsg] = useState(null);
    
    // form data
    const [selected_gallery, setSelectedGallery] = useState('default-gallery');
    const [new_gallery_name, setNewGalleryName] = useState('');
    const [images, setImages] = useState([]);

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
        try {
            // get gallery name
            let gallery_name = selected_gallery === 'new-gallery-141715288475' ? new_gallery_name : selected_gallery;

            if (gallery_name.includes(' ')) {
                setErrorMsg('spaces in gallery name is not allowed!');
            } else {

                // upload images
                for (const image of images) {
                    const formData = new FormData();
    
                    // set post data
                    formData.append('token', user_token);
                    formData.append('gallery', gallery_name);
                    formData.append('image', image);
    
                    // send request
                    const response = await fetch(api_url + '/media/upload', {
                        method: 'POST',
                        body: formData
                    });
    
                    // get response
                    const result = await response.json();
    
                    // check response
                    if (result.status === 'success') {
                        console.log('uploaded!');
                    } else {
                        setErrorMsg(result.message);
                    }
                }
            }
        } catch (error) {
            console.error('Error during upload:', error);
        }
    };

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

                                            {error_msg !== null && (
                                                <ErrorBoxComponent error_msg={error_msg}/>
                                            )}

                                            <div className='upload-form'>
                                                <label htmlFor='images' className='form-label'>Image(s)</label>
                                                <input type='file' id='images' name='images[]' className='form-control mb-3' multiple accept='image/*' onChange={handleImageChange}/>

                                                <label htmlFor='galleryName' className='form-label'>Gallery Name</label>
                                                <select id='galleryName' name='galleryName' className='form-control form-control-lg mb-3' onChange={handleGalleryChange}>
                                                    <option value='gallery-1'>gallery-1</option>
                                                    <option value='gallery-2'>gallery-2</option>
                                                    <option value='gallery-3'>gallery-3</option>
                                                    <option value='new-gallery-141715288475'>New gallery</option>
                                                </select>

                                                {selected_gallery === 'new-gallery-141715288475' && (
                                                    <div>
                                                        <label htmlFor='newGalleryName' className='form-label'>New Gallery Name</label>
                                                        <input type='text' id='newGalleryName' name='newGalleryName' className='form-control form-control-lg mb-3' placeholder='New Gallery name' onChange={handleNewGalleryNameChange}/>
                                                    </div>
                                                )}

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
