// engal components
import MainNavigationComponent from "./sub-component/navigation/MainNavigationComponent"
import UserNavigationComponent from "./sub-component/navigation/UserNavigationComponent"

export default function UploadComponent() {
    return (
        <div>
            <MainNavigationComponent/>            
            <UserNavigationComponent/>
            <div className="app-component">
                <p>! upload !</p>
            </div>
        </div>
    )
}
