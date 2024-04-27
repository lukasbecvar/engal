import { Link } from 'react-router-dom'

/**
 * User panel navigation
 */
export default function UserNavigationComponent(props) {
    const text_color = props.roles.includes('ROLE_ADMIN') ? 'red' : 'green';

    return (
        <div className="user-navbar">
            <span>➜</span>
            <Link to="/" className="sub-navigation-link">home</Link>

            <div className="user-data">
                <p className={`color-${text_color}`}>{props.username}</p>
            </div>
        </div>
    )
}
