import { useState } from 'react';

// import engal components
import ErrorBoxComponent from '../alerts/ErrorBoxComponent';

export default function ProfilePicChangeComponent(props) 
{
    // form data
    const [image, setImage] = useState([]);

    // handle image input change
    function handleImageChange(event) {
        setImage(Array.from(event.target.files));
    };

    function handleChangePic() {
        if (image.length < 1) {
            console.log('Error: File input is empty');
        } else {
            const reader = new FileReader();
    
            // read the content of the first selected image as a data URL
            reader.readAsDataURL(image[0]);
    
            // set up the onload callback to handle the result
            reader.onload = function () {
                const base64Image = reader.result.split(',')[1];
                console.log('Base64 Image:', base64Image);
            };
        }
    }

    return (
        <center>
            <div className="form dark-table bg-dark border">
                <h2 className="form-title">Profile pic change</h2>

                <ErrorBoxComponent error_msg="idk"/>

                <label htmlFor='images' className='form-label'>Image(s)</label>
                <input type='file' id='images' name='images[]' className='form-control mb-3' accept='image/*' onChange={handleImageChange}/>
    

                <div className='m-3 justify-content-center'>
                    <button type='button' className='btn btn-success btn-block btn-lg gradient-custom-4 text-light' onClick={handleChangePic}>Upload</button>
                </div>

                {props.show_panel_element}
            </div>
        </center> 
    );
}
