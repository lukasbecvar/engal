import { Link } from 'react-router-dom'

/*
 * Auth components footer element
*/
export default function AuthFooterComponent() {
    return(
        <div className="auth-footer">
            <p>reset api access point <Link to="/reset/api">here</Link></p>
        </div>
    )    
}
