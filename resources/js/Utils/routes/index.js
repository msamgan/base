import { user } from '@/Utils/routes/user.js'

export const routes = {
    business: {
        update: (id) => route('business.update', id),
    },
    notifications: {
        index: route('notification.index'),
    },
    user,
}
