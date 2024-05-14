// engal components
import GalleryListComponent from "./GalleryListComponent"
import BreadcrumbComponent from "./navigation/BreadcrumbComponent"
import NavigationComponent from "./navigation/NavigationComponent"

/**
 * Component main app (user) dashboard
 */
export default function DashboardComponent() {
    return (
        <div>
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            <div className="app-component">
                <GalleryListComponent/>
            </div>
        </div>
    )
}
