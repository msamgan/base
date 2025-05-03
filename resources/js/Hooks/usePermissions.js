import { usePage } from '@inertiajs/react'

export default function usePermissions() {
    const { auth } = usePage().props
    const can = (permission) => auth.user.access.filter((p) => p.name === permission).length > 0
    return { can }
}
