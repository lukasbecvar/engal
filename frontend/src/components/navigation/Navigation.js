import { Outlet, Link } from "react-router-dom";

const Layout = () => {

    // return navigation panel
    return (
        <div>
            <nav className="navigation">
                <ul>
                    <li><Link className="nav-link" to="/">LIST</Link></li>
                    <li><Link className="nav-link" to="/upload">UPLOAD</Link></li>
                </ul>
            </nav>
        <Outlet />
        </div>
    )
};
 
export default Layout;