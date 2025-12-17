export interface Auth {
    self: User
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    app: string,
    self: User
};

export interface User {
    id: number,
    username: string,
    email: string,
    email_verified_at?: string,
    description?: string,
    role: 'Admin' | 'Artist' | 'User',
    created_at: string,
    updated_at: string
}
