export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string,
    quote: { message: string; author: string }
};

export interface User {
    id: number,
    email: string,
    role: Role,
    name?: string,
    created_at: string,
    updated_at: string
}

export enum Role {
    Admin = 'Admin',
    Artist = 'Artist',
    User = 'User'
}



export interface Song {
    id: number,
    title: string,
    duration: number,
    is_explicit: boolean,
    created_at: string,
    updated_at: string
}

export interface OrderedSong extends Song {
    index: number
}

export interface Playlist {
    id: number,
    creator_id: string,
    name: string,
    description?: string,
    is_album: boolean,
    created_at: string,
    updated_at: string
}

