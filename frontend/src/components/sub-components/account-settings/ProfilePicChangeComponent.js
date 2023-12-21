import { useState } from 'react';

// import config values
import { DEV_MODE } from '../../../config';

// import engal utils
import { appReload } from '../../../utils/AppUtils';
import { getApiUrl } from '../../../utils/ApiUtils';
import { getUserToken } from '../../../utils/AuthUtils';

// import engal components
import ErrorBoxComponent from '../alerts/ErrorBoxComponent';

export default function ProfilePicChangeComponent(props) 
{
    // state variables for managing component state
    const [error_msg, setError] = useState(null);

    // form data
    const [image, setImage] = useState([]);

    // retrieve API URL from local storage
    let api_url = getApiUrl();

    // get current user token
    let user_token = getUserToken();

    // handle image input change
    function handleImageChange(event) {
        setImage(Array.from(event.target.files));
    };

    function handleChangePic() {
        if (image.length < 1) {
            setError('your image input is empty');
        } else {
            const reader = new FileReader();
    
            // read the content of the first selected image as a data URL
            reader.readAsDataURL(image[0]);
    
            // set up the onload callback to handle the result
            reader.onload = function () {
                const base64_image = reader.result.split(',')[1];
                
                // update profile pic
                updateProfilePic(base64_image)
            };
        }
    }

    async function updateProfilePic(base64_image) {
        try {
            const formData = new FormData();

            // build request data
            formData.append('token', user_token);
            formData.append('image', base64_image);

            // make post request
            const response = await fetch(api_url + '/account/settings/pic', {
                method: 'POST',
                body: formData
            });

            // get response
            const result = await response.json();

            // check error
            if (!response.ok) {
                if (DEV_MODE) {
                    console.error('error: ', response.status);
                }
                return;
            } else {
                // check if status is success
                if (result.status === 'success') {
                    appReload();
                } else {
                    setError(result.message);
                }
            }
        } catch (error) {
            if (DEV_MODE) {
                console.error('error: ', error);
            }
        }
    }

    return (
        <center>
            <div className="form dark-table bg-dark border">
                <h2 className="form-title">Profile pic change</h2>

                {/* error box alert */}
                {error_msg !== null && (
                    <ErrorBoxComponent error_msg={error_msg}/>
                )}

                {/* image input */}
                <label htmlFor='images' className='form-label'>Image(s)</label>
                <input type='file' id='images' name='images[]' className='form-control mb-3' accept='image/*' onChange={handleImageChange}/>
    
                {/* form submit button */}
                <div className='m-3 justify-content-center'>
                    <button type='button' className='btn btn-success btn-block btn-lg gradient-custom-4 text-light' onClick={handleChangePic}>Upload</button>
                </div>

                {/* back to user settings button */}
                {props.show_panel_element}
            </div>
        </center> 
    );
}
