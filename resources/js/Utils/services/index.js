import { role } from '@/Utils/services/role.js'
import { user } from '@/Utils/services/user.js'

export const services = {
    permissions: route('service.permissions'),
    role,
    user,
}
