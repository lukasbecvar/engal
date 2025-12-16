import { Link } from 'react-router-dom'

// import fontawesome
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSignOutAlt, faUpload } from '@fortawesome/free-solid-svg-icons'

/**
 * Main dashboard navigation
 */
export default function NavigationComponent() {
    return (
        <div className="main-nav">
            <div>
                <h2 className="nav-header">Engal</h2>
            </div>
            <div className="right-content">
                {/* upload button */}
                <Link className="m-r-1" to="/upload">
                    <FontAwesomeIcon icon={faUpload}/>
                </Link>

                {/* logout button */}
                <Link to="/logout">
                    <FontAwesomeIcon icon={faSignOutAlt}/>
                </Link>
            </div>
        </div>
    )
}
