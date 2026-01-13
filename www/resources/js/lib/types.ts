export interface ApiResult<T extends object> {
    data?: T,
    error?: {
        status: number,
        field?: string,
        message?: string
    }
}



export interface User {
    id: string,
    username: string,
    email: string,
    description?: string,
    created_at: string,
    updated_at: string
}



export interface Song {
    id: string,
    title: string,
    artist: string,
    url: string,
    cover_url: string,
    date: string,
    duration: number,
    genre: string,
    // is_explicit: boolean,
    created_at: string,
    updated_at: string
}

export interface Playlist {
    id: string,
    user_id: string,
    name: string,
    description?: string,
    public: boolean,
    is_album: boolean,
    created_at: string,
    updated_at: string,

    songs_count: number
}
