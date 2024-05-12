import { Link } from 'react-router-dom'

/*
 * Auth components footer element
*/
export default function AuthFooterComponent() {
    return(
        <div className="auth-footer">
            <p className="form-link">reset api access point <Link to="/reset/api" className="color-blue">here</Link></p>
        </div>
    )    
}
