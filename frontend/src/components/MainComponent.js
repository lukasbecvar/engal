// react router
import { BrowserRouter, Routes, Route } from "react-router-dom";

// app elements
import NotFound from "./errors/NotFound.js"
import Navigation from "./navigation/Navigation.js"

// app components
import UploadComponent from "./UploadComponent.js";
import GalleryBrowserComponent from "./GalleryBrowserComponent.js";
import GalleryListComponent from "./GalleryListComponent.js"
import DeleteCompnent from "./subcomponents/DeleteCompnent.js";
import EditComponent from "./subcomponents/EditComponent.js";

const MainComponent = () => {

    // react router component
    return (
        <main>
            <BrowserRouter>
                <Routes>
                    <Route path="/" element={<Navigation />}>
                        <Route index element={<GalleryListComponent />} />
                        <Route path="list" element={<GalleryListComponent />} />
                        <Route path="browse" element={<GalleryBrowserComponent />} />
                        <Route path="upload" element={<UploadComponent />} />
                        <Route path="edit" element={<EditComponent />} />
                        <Route path="delete" element={<DeleteCompnent />} />
                        <Route path="*" element={<NotFound />} />
                    </Route>
                </Routes>
            </BrowserRouter>
        </main>
    );
    
};
  
export default MainComponent;
