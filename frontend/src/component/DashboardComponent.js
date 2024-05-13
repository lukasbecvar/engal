// engal components
import GalleryListComponent from "./GalleryListComponent"
import MainNavigationComponent from "./sub-component/navigation/MainNavigationComponent"
import UserNavigationComponent from "./sub-component/navigation/UserNavigationComponent"

/**
 * Component main app (user) dashboard
 */
export default function DashboardComponent() {
    return (
        <div>
            <MainNavigationComponent/>            
            <UserNavigationComponent/>
            <div className="app-component">
                <GalleryListComponent/>
            </div>
        </div>
    )
}
