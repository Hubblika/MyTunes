import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { ApiError } from './types';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    return toUrl(urlToCheck) === currentUrl;
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function describeError(name: ApiError): string {
    switch (name) {
        case ApiError.InvalidEmail:
            return 'Please enter a valid email address';
        case ApiError.InvalidPassword:
            return 'Your password must be at least 8 characters long';
        case ApiError.IncorrectPassword:
            return 'Incorrect password, try again';
        case ApiError.UserNotFound:
            return 'Could not find this user';
        case ApiError.SongNotFound:
            return 'Could not find this song';
        case ApiError.PlaylistNotFound:
            return 'Could not find this playlist';
        default:
            return 'Unknown error';
    }
}
