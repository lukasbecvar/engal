import { BrowserRouter as Router, Routes, Route} from 'react-router-dom'

// engal components
import UploadComponent from './component/UploadComponent'
import LogoutComponent from './component/auth/LogoutComponent'
import DashboardComponent from './component/DashboardComponent'
import NotFoundComponent from './component/error/NotFoundComponent'
import VideoPlayerComponent from './component/VideoPlayerComponent'
import GalleryBrowserComponent from './component/GalleryBrowserComponent'

/**
 * Component defining the routing structure of the application.
 */
export default function AppRouter() {
    return (
        <Router>
            <Routes>
                {/* logout component */}
                <Route exact path="/logout" element={<LogoutComponent/>}/>

                {/* upload component */}
                <Route exact path="/upload" element={<UploadComponent/>}/>
                
                {/* default components */}
                <Route exact path="/" element={<DashboardComponent/>}/>

                {/* gallery browser */}
                <Route exact path="/gallery" element={<GalleryBrowserComponent/>}/>

                {/* video player */}
                <Route exact path="/video" element={<VideoPlayerComponent/>}/>

                {/* not found component */}
                <Route path="*" element={<NotFoundComponent/>}/>
            </Routes>
        </Router>
    )
}
