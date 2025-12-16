/**
 * Component element loading
 */
export default function LoadingComponent() {
    return (
        <div className="loading-container">
            <div className="loading-ring" aria-label="Loading" />
            <div className="loading-text">Loading…</div>
        </div>
    );
}
