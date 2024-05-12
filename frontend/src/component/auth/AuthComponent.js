import { BrowserRouter as Router, Routes, Route} from 'react-router-dom'

// engal components
import LoginComponent from './LoginComponent'
import RegisterComponent from './RegisterComponent'
import NotFoundComponent from '../sub-component/error/NotFoundComponent'
import ResetApiUrlComponent from '../sub-component/ResetApiUrlComponent'

/**
 * Component Auth/user router
 */
export default function AuthComponent() {
    return (
        <Router>
            <Routes>
                <Route exact path="/" element={<LoginComponent/>}/>
                <Route exact path="/login" element={<LoginComponent/>}/>
                <Route exact path="/register" element={<RegisterComponent/>}/>

                {/* component for reset api access point (url) */}
                <Route exact path="/reset/api" element={<ResetApiUrlComponent/>}/>
                <Route path="*" element={<NotFoundComponent/>}/>
            </Routes>
        </Router>
    )
}
