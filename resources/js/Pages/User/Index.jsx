import Master from '@/Layouts/Master.jsx'
import { Head } from '@inertiajs/react'
import { permissions } from '@/Utils/permissions/index.js'
import { useEffect, useState } from 'react'
import Actions from '@/Components/helpers/Actions.jsx'
import Name from '@/Components/helpers/Name.jsx'
import ActiveBadge from '@/Components/helpers/ActiveBadge.jsx'
import OffCanvasButton from '@/Components/off_canvas/OffCanvasButton.jsx'
import Table from '@/Components/layout/Table.jsx'
import { pageObject } from '@/Pages/User/helper.js'
import PageHeader from '@/Components/PageHeader.jsx'
import OffCanvas from '@/Components/off_canvas/OffCanvas.jsx'
import Form from '@/Pages/User/Partials/Form.jsx'
import { roles as rcRoles } from '@actions/RoleController.js'
import { destroy, show, users as _users } from '@actions/UserController.js'
import usePermissions from '@/Hooks/usePermissions'
import EditActionButton from '@/Components/EditActionButton.jsx'
import DeleteActionButton from '@/Components/DeleteActionButton.jsx'
import CreateActionButton from '@/Components/CreateActionButton.jsx'

export default function Index() {
    const { can } = usePermissions()

    const [users, setUsers] = useState([])
    const [data, setData] = useState([])
    const [user, setUser] = useState(null)
    const [loading, setLoading] = useState(true)
    const [pageData, setPageData] = useState(pageObject(null))
    const [roles, setRoles] = useState([])

    const getUsers = async () => setUsers(await _users.data({}))

    const getRoles = async () => setRoles(await rcRoles.data({}))

    const getUser = async (id) => setUser(await show.data({ params: { user: id } }))

    const editUser = (user) => {
        getUser(user.id).then()
        setPageData(pageObject(user))
    }

    const processUser = (user) => {
        return {
            Name: <Name value={user.name} />,
            Roles: user.roles.map((role) => role.display_name).join(', '),
            Status: <ActiveBadge value={'Active'} />,
            Actions: (
                <Actions
                    edit={<EditActionButton module={'user'} onClick={() => editUser(user)} />}
                    deleteAction={
                        <DeleteActionButton
                            module={'user'}
                            route={destroy.route({ user: user.id })}
                            refresh={getUsers}
                        />
                    }
                />
            ),
        }
    }

    useEffect(() => {
        if (can(permissions.user.list)) {
            getUsers()
                .then()
                .finally(() => setLoading(false))
        }

        getRoles().then()
    }, [])

    useEffect(() => {
        setData(users.map((user) => processUser(user)))
    }, [users])

    return (
        <Master>
            <Head title="Users" />

            <PageHeader
                title={'Users'}
                subtitle={'Find all of your business’s users and there associated details.'}
                action={
                    <CreateActionButton
                        module={'user'}
                        onClick={() => {
                            setUser(null)
                            setPageData(pageObject(null))
                        }}
                    />
                }
            ></PageHeader>

            {can([permissions.user.view, permissions.user.update, permissions.user.create]) && (
                <OffCanvas id="userFormCanvas" title={pageData.title}>
                    <Form getUsers={getUsers} roles={roles} user={user} />
                </OffCanvas>
            )}

            <div className="col-12">
                <Table data={data} loading={loading} permission={can(permissions.user.list)} />
            </div>
        </Master>
    )
}
