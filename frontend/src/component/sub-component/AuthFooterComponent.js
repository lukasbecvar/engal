import { Link } from 'react-router-dom'

/*
 * Auth components footer element
*/
export default function AuthFooterComponent() {
    return(
        <div className="auth-footer">
            <p className="form-link">
                <Link to="/reset/api" className="color-blue">Settings</Link>
            </p>
                
            {/* Developer website link */}
            <a href='https://becvar.xyz/' target='_blank' className='dev-link color-blue'>Lukáš Bečvář</a>
        </div>
    )    
}
