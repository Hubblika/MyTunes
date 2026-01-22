import type { User } from '@/lib/types';

export interface Auth {
    self: Self
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    app: string,
    self: Self
};

export interface Self extends User {
    email_verified_at?: string,
}



export interface _Song {
    uuid: string,
    title: string,
    artist: string,
    album: string,
    url: string,
    cover_url: string,
    date: string,
    duration: number,
    genre?: string,
    created_at?: string,
    updated_at?: string
}

export interface Song extends _Song {
    authors: User[]
}

export interface _Playlist {
    uuid: string,
    creator_id: string,
    name: string,
    description?: string,
    public: boolean,
    is_album: boolean,
    created_at?: string,
    updated_at?: string
}

export interface Playlist extends _Playlist {
    creator: User,
    songs: Song[]
}



export interface ButtonProps {
    type?: 'button' | 'submit' | 'menu' | 'reset',
    href?: string,
    class?: ClassValue,
    disabled?: boolean
}
