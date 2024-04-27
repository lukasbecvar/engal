import { BrowserRouter as Router, Routes, Route} from 'react-router-dom'

// engal components
import LogoutComponent from './component/auth/LogoutComponent'
import DashboardComponent from './component/DashboardComponent'
import NotFoundComponent from './component/sub-component/error/NotFoundComponent'

/**
 * Component defining the routing structure of the application.
 */
export default function AppRouter() {
    return (
        <Router>
            <Routes>
                <Route exact path="/logout" element={<LogoutComponent/>}/>
                
                <Route exact path="/" element={<DashboardComponent/>}/>
                <Route path="*" element={<NotFoundComponent/>}/>
            </Routes>
        </Router>
    )
}
