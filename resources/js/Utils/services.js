export const services = {
    menu: route('service.menu'),
    permissions: route('service.permissions'),
    roles: route('service.roles'),
    role: {
        show: (id) => route('service.role.show', id),
        destroy: (id) => route('service.role.destroy', id),
    },
}

export const routes = {
    business: {
        update: (id) => route('business.update', id),
    },
    role: {
        store: route('role.store'),
        update: (id) => route('role.update', id),
    },
    user: {
        store: route('user.store'),
        update: (id) => route('user.update', id),
    },
    notifications: {
        index: route('notification.index'),
    },
}
