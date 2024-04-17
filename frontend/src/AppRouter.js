import { BrowserRouter as Router, Routes, Route} from 'react-router-dom'
import DashboardComponent from './component/DashboardComponent'
import NotFoundComponent from './component/sub-component/NotFoundComponent'

export function AppRouter() {
    return (
        <Router>
            <Routes>
                <Route exact path="/" element={<DashboardComponent/>}/>
                <Route path="*" element={<NotFoundComponent/>}/>
            </Routes>
        </Router>
    )
}
