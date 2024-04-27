import { Link } from 'react-router-dom'

// import fontawesome
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faSignOutAlt } from '@fortawesome/free-solid-svg-icons';

/**
 * Main dashboard navigation
 */
export default function MainNavigationComponent() {
    return (
        <div className="main-nav">
            <div>
                <h2 className="nav-header">Engal</h2>
            </div>
            <div className="right-content">
                <Link to='/logout'>
                    <FontAwesomeIcon icon={faSignOutAlt}/>
                </Link>
            </div>
        </div>
    )
}
