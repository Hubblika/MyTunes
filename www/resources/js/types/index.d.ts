export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string,
    quote: { message: string; author: string }
};

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

export interface OrderedSong extends Song {
    index: number
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

