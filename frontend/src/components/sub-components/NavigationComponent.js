import { appReload } from "../../utils/AppUtils";

export default function NavigationComponent() {
    return (
        <nav className='navbar navbar-expand-lg navbar-dark bg-dark'>
            <div className='container-fluid'>
                <button className='navbar-brand' onClick={appReload}>
                    Engal
                </button>
            </div>
        </nav>
    );
}
