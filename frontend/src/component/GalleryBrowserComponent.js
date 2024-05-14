// engal components
import BreadcrumbComponent from "./navigation/BreadcrumbComponent"
import NavigationComponent from "./navigation/NavigationComponent"

/**
 * Gallery content browser component
 */
export default function GalleryBrowserComponent() {
    // get url query parameters
    const urlParams = new URLSearchParams(window.location.search);

    return (
        <div>
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            <div className="app-component">
                <p>gallery: {urlParams.get('name')}</p>
            </div>
        </div>
    )
}
