// engal components
import MainNavigationComponent from "./sub-component/navigation/MainNavigationComponent"
import UserNavigationComponent from "./sub-component/navigation/UserNavigationComponent"

/**
 * Gallery content browser component
 */
export default function GalleryBrowserComponent() {
    // get url query parameters
    const urlParams = new URLSearchParams(window.location.search);

    return (
        <div>
            <MainNavigationComponent/>            
            <UserNavigationComponent/>
            <div className="app-component">
                <p>gallery: {urlParams.get('name')}</p>
            </div>
        </div>
    )
}
