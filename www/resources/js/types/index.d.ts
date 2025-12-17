export interface Auth {
    user: User
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    name: string,
    auth: Auth
};

export interface User {
    id: number,
    username: string,
    email: string,
    email_verified_at?: string,
    created_at: string,
    updated_at: string
}
