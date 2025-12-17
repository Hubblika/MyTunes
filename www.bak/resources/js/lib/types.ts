export interface ApiResult<T extends object> {
    data?: T,
    error?: {
        status: number,
        name: ApiError,
        message?: string
    }
}

export enum ApiError {
    Unknown = 'UNKNOWN',

    // 400 Bad Request
    InvalidContentType = 'INVALID_CONTENT_TYPE',
    InvalidEmail = 'INVALID_EMAIL',
    InvalidPassword = 'INVALID_PASSWORD',

    // 401 Unauthorized
    Unauthorized = 'UNAUTHORIZED',
    IncorrectPassword = 'INCORRECT_PASSWORD',

    // 404 Not Found
    UserNotFound = 'USER_NOT_FOUND',
    SongNotFound = 'SONG_NOT_FOUND',
    PlaylistNotFound = 'PLAYLIST_NOT_FOUND'
}



export interface User {
    id: number,
    email: string,
    role: Role,
    name?: string,
    description?: string,
    created_at: string,
    updated_at: string
}

export enum Role {
    Admin = 'Admin',
    Artist = 'Artist',
    User = 'User'
}



export interface Song {
    uuid: string,
    title: string,
    created_at: string
    duration: number,
    is_explicit: boolean,
}

export interface Playlist {
    uuid: string,
    creator_id: number,
    name: string,
    description?: string,
    is_album: boolean,
    created_at: string,
    updated_at: string
}

