import { usePage } from '@inertiajs/react'

export default function usePermissions() {
    const { auth } = usePage().props
    const can = (permission) => {
        if (Array.isArray(permission)) {
            return permission.some((p) => auth.user.access.some((userPermission) => userPermission.name === p))
        }

        return auth.user.access.some((p) => p.name === permission)
    }
    return { can }
}
