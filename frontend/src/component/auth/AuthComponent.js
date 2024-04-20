import { BrowserRouter as Router, Routes, Route} from 'react-router-dom'

// engal components
import LoginComponent from './LoginComponent'
import RegisterComponent from './RegisterComponent'
import NotFoundComponent from '../sub-component/NotFoundComponent'

/**
 * Component Auth/user router
 */
export function AuthComponent() {
    return (
        <Router>
            <Routes>
                <Route exact path="/" element={<LoginComponent/>}/>
                <Route exact path="/login" element={<LoginComponent/>}/>
                <Route exact path="/register" element={<RegisterComponent/>}/>
                <Route path="*" element={<NotFoundComponent/>}/>
            </Routes>
        </Router>
    )
}
