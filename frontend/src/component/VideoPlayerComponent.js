import React, { useEffect, useRef, useState } from 'react'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faPlay, faPause, faVolumeUp, faVolumeMute, faExpand } from '@fortawesome/free-solid-svg-icons'

// engal components
import LoadingComponent from './sub-component/LoadingComponent'
import ErrorMessageComponent from './error/ErrorMessageComponent'
import BreadcrumbComponent from './navigation/BreadcrumbComponent'
import NavigationComponent from './navigation/NavigationComponent'

// engal utils
import { DEV_MODE } from '../config'

export default function VideoPlayerComponent() {
    // get storage data
    const apiUrl = localStorage.getItem('api-url')

    // get video token
    const videoToken = new URLSearchParams(window.location.search).get('media_token')

    // set states
    const [loading, setLoading] = useState(true)
    const [mediaInfo, setMediaInfo] = useState(null)
    const [mediaUrl, setMediaUrl] = useState(null)
    const [isPlaying, setIsPlaying] = useState(false)
    const [duration, setDuration] = useState(0)
    const [displayLength, setDisplayLength] = useState(null)
    const [currentTime, setCurrentTime] = useState(0)
    const [playbackError, setPlaybackError] = useState(null)
    const [volume, setVolume] = useState(0.85)
    const [lastVolume, setLastVolume] = useState(0.85)
    const [isMuted, setIsMuted] = useState(false)
    const [showControls, setShowControls] = useState(true)
    const refreshTimerRef = useRef(null)
    const videoRef = useRef(null)
    const playerShellRef = useRef(null)
    const controlsTimerRef = useRef(null)

    // clear refresh timer on unmount
    useEffect(() => {
        return () => {
            if (refreshTimerRef.current) {
                clearTimeout(refreshTimerRef.current)
            }
            if (controlsTimerRef.current) {
                clearTimeout(controlsTimerRef.current)
            }
        }
    }, [])

    useEffect(() => {
        const handleKey = (event) => {
            const videoEl = videoRef.current
            if (!videoEl) return
            if (['INPUT', 'TEXTAREA', 'SELECT', 'OPTION'].includes(document.activeElement.tagName)) return

            switch (event.key) {
            case ' ':
                event.preventDefault()
                togglePlayPause()
                revealControls()
                break
            case 'ArrowRight':
                event.preventDefault()
                seekRelative(5)
                revealControls()
                break
            case 'ArrowLeft':
                event.preventDefault()
                seekRelative(-5)
                revealControls()
                break
            case 'ArrowUp':
                event.preventDefault()
                adjustVolume(0.05)
                revealControls()
                break
            case 'ArrowDown':
                event.preventDefault()
                adjustVolume(-0.05)
                revealControls()
                break
            default:
                break
            }
        }

        window.addEventListener('keydown', handleKey)
        return () => window.removeEventListener('keydown', handleKey)
    }, [volume])

    const applyMediaUrl = (url) => {
        setMediaUrl(url)

        const videoEl = videoRef.current
        if (!videoEl) {
            return
        }

        const currentTime = videoEl.currentTime || 0
        const wasPaused = videoEl.paused

        try {
            videoEl.src = url
            if (currentTime > 0) {
                videoEl.currentTime = currentTime
            }
            if (!wasPaused) {
                videoEl.play().catch(() => {})
            }
        } catch (e) {
            if (DEV_MODE) {
                console.log('Failed to apply refreshed media url', e)
            }
        }
    }

    const scheduleRefresh = (expiresInSeconds) => {
        const buffer = 120 // refresh 2 minutes before expiration
        const delayMs = Math.max(30000, (expiresInSeconds - buffer) * 1000)

        if (refreshTimerRef.current) {
            clearTimeout(refreshTimerRef.current)
        }

        refreshTimerRef.current = setTimeout(() => {
            fetchPresignedUrl('refresh')
        }, delayMs)
    }

    const fetchPresignedUrl = async (reason = 'initial') => {
        try {
            const presignedResponse = await fetch(apiUrl + '/api/media/presigned?media_token=' + videoToken, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': '*/*',
                },
            })
            if (!presignedResponse.ok) {
                throw new Error('presigned url request failed with status ' + presignedResponse.status)
            }
            const presignedData = await presignedResponse.json()
            if (presignedData.status === 'success') {
                const expiresIn = presignedData.expires_in ?? 900
                applyMediaUrl(presignedData.url)
                scheduleRefresh(expiresIn)
            } else {
                setPlaybackError('Unable to load video (decryption/storage error).')
                if (DEV_MODE) {
                    console.log('Failed to fetch presigned url (' + reason + '): ' + presignedData.message)
                }
            }
        } catch (error) {
            if (DEV_MODE) {
                console.log('Error to fetch presigned url (' + reason + '): ' + error)
            }
            setPlaybackError('Unable to load video (decryption/storage error).')
        }
    }

    // fetch video details
    useEffect(() => {
        const fetchUserData = async () => {
            try {
                // build request
                const response = await fetch(apiUrl + '/api/media/info?media_token=' + videoToken, {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                        'Accept': '*/*',
                    },
                })

                // get response data
                const data = await response.json()
                    
                // check if user token is valid
                if (data.status === 'success') {
                    setMediaInfo(data.media_info)
                    setDisplayLength(data.media_info.length)
                    await fetchPresignedUrl('initial')
                } else {
                    setPlaybackError(data.message ?? 'Unable to load video')
                    return                    
                }
            } catch (error) {
                if (DEV_MODE) {
                    console.log('Error to fetch user data: ' + error)
                }
                setPlaybackError('Unable to load video (connection/storage error).')
            } finally {
                setLoading(false)
            }
        }
        fetchUserData()
    }, [apiUrl, videoToken])

    useEffect(() => {
        if (videoRef.current) {
            videoRef.current.volume = volume
            videoRef.current.muted = isMuted
        }
    }, [volume, isMuted])

    const togglePlayPause = () => {
        const videoEl = videoRef.current
        if (!videoEl) return
        if (videoEl.paused) {
            videoEl.play().then(() => {
                setIsPlaying(true)
                revealControls()
            }).catch(() => setIsPlaying(false))
        } else {
            videoEl.pause()
            setIsPlaying(false)
            setShowControls(true)
        }
    }

    const handleTimeUpdate = () => {
        const videoEl = videoRef.current
        if (!videoEl) return
        setCurrentTime(videoEl.currentTime)
    }

    const handleLoadedMetadata = () => {
        const videoEl = videoRef.current
        if (!videoEl) return
        setDuration(videoEl.duration || 0)
        setCurrentTime(videoEl.currentTime || 0)
        if (videoEl.duration && isFinite(videoEl.duration)) {
            setDisplayLength(formatTime(videoEl.duration))
        }
    }

    const handleSeek = (event) => {
        const videoEl = videoRef.current
        if (!videoEl) return
        const newTime = parseFloat(event.target.value)
        videoEl.currentTime = newTime
        setCurrentTime(newTime)
        revealControls()
    }

    const seekRelative = (deltaSeconds) => {
        const videoEl = videoRef.current
        if (!videoEl) return
        const newTime = Math.max(0, Math.min((videoEl.currentTime || 0) + deltaSeconds, duration || 0))
        videoEl.currentTime = newTime
        setCurrentTime(newTime)
    }

    const handleVolumeChange = (event) => {
        const videoEl = videoRef.current
        if (!videoEl) return
        const newVolume = parseFloat(event.target.value)
        setVolume(newVolume)
        videoEl.volume = newVolume
        if (newVolume === 0) {
            setIsMuted(true)
        } else {
            setIsMuted(false)
            setLastVolume(newVolume)
        }
    }

    const toggleMute = () => {
        const videoEl = videoRef.current
        if (!videoEl) return
        if (isMuted || volume === 0) {
            const restored = lastVolume || 0.8
            setVolume(restored)
            videoEl.volume = restored
            setIsMuted(false)
        } else {
            setLastVolume(volume)
            setVolume(0)
            videoEl.volume = 0
            setIsMuted(true)
        }
    }

    const adjustVolume = (delta) => {
        const videoEl = videoRef.current
        if (!videoEl) return
        const newVolume = Math.min(1, Math.max(0, (videoEl.volume || 0) + delta))
        setVolume(newVolume)
        videoEl.volume = newVolume
        if (newVolume === 0) {
            setIsMuted(true)
        } else {
            setIsMuted(false)
            setLastVolume(newVolume)
        }
    }

    const toggleFullscreen = () => {
        const shell = playerShellRef.current
        if (!shell) return
        if (document.fullscreenElement) {
            document.exitFullscreen().catch(() => {})
        } else {
            shell.requestFullscreen().catch(() => {})
        }
    }

    const formatTime = (timeInSeconds) => {
        if (!isFinite(timeInSeconds)) return '0:00'
        const minutes = Math.floor(timeInSeconds / 60)
        const seconds = Math.floor(timeInSeconds % 60)
        return `${minutes}:${seconds.toString().padStart(2, '0')}`
    }

    // update length when duration is known
    useEffect(() => {
        if (duration && isFinite(duration)) {
            setDisplayLength(formatTime(duration))
        }
    }, [duration])

    const scheduleHideControls = () => {
        if (!isPlaying) return
        if (controlsTimerRef.current) {
            clearTimeout(controlsTimerRef.current)
        }
        controlsTimerRef.current = setTimeout(() => {
            setShowControls(false)
        }, 1800)
    }

    const revealControls = () => {
        setShowControls(true)
        if (controlsTimerRef.current) {
            clearTimeout(controlsTimerRef.current)
        }
        scheduleHideControls()
    }

    // render loading component
    if (loading) {
        return <LoadingComponent/>
    }

    if (playbackError) {
        return <ErrorMessageComponent message={playbackError}/>
    }

    return (
        <div className="video-player-component">
            <NavigationComponent/>            
            <BreadcrumbComponent/>
            <div className="video-player">
                <div className="video-info-line top">
                    {mediaInfo?.name} <span className="media-length-info">{displayLength ?? mediaInfo?.length ?? ''}</span>
                </div>
                <div className="player-shell" ref={playerShellRef}>
                    <video
                        className="video-frame"
                        ref={videoRef}
                        onClick={togglePlayPause}
                        onMouseMove={revealControls}
                        onTimeUpdate={handleTimeUpdate}
                        onLoadedMetadata={handleLoadedMetadata}
                        onEnded={() => setIsPlaying(false)}
                        onPlay={() => setIsPlaying(true)}
                        onPause={() => setIsPlaying(false)}
                        onError={() => setPlaybackError('Unable to play video (decryption/storage error).')}
                    >
                        <source src={mediaUrl ?? (apiUrl + "/api/media/content?media_token=" + videoToken)} type="video/mp4" />
                        Your browser does not support the video tag.
                    </video>
                    <div
                        className={`player-controls ${showControls ? 'visible' : 'hidden'}`}
                        onMouseMove={revealControls}
                    >
                        <div className="controls-row">
                            <button className="control-button" onClick={togglePlayPause} aria-label={isPlaying ? 'Pause' : 'Play'}>
                                <FontAwesomeIcon icon={isPlaying ? faPause : faPlay}/>
                            </button>
                            <div className="time-display">
                                {formatTime(currentTime)} / {formatTime(duration)}
                            </div>
                            <div className="spacer" />
                            <button className="control-button" onClick={toggleMute} aria-label="Mute">
                                <FontAwesomeIcon icon={isMuted || volume === 0 ? faVolumeMute : faVolumeUp}/>
                            </button>
                            <input
                                type="range"
                                min="0"
                                max="1"
                                step="0.01"
                                value={volume}
                                onChange={handleVolumeChange}
                                className="volume-slider"
                                aria-label="Volume"
                            />
                            <button className="control-button" onClick={toggleFullscreen} aria-label="Fullscreen">
                                <FontAwesomeIcon icon={faExpand}/>
                            </button>
                        </div>
                        <input
                            type="range"
                            min="0"
                            max={duration || 0}
                            step="0.01"
                            value={Math.min(currentTime, duration || 0)}
                            onChange={handleSeek}
                            className="progress-slider"
                            aria-label="Seek"
                            style={{
                                background: `linear-gradient(90deg, rgba(77, 214, 255, 0.6) ${(duration ? (currentTime / duration) * 100 : 0)}%, rgba(255, 255, 255, 0.08) ${(duration ? (currentTime / duration) * 100 : 0)}%)`
                            }}
                        />
                    </div>
                </div>
            </div>
        </div>
    )
}
