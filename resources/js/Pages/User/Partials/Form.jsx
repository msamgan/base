import { useForm } from '@inertiajs/react'
import TextInput from '@/Components/TextInput.jsx'
import InputLabel from '@/Components/InputLabel.jsx'
import InputError from '@/Components/InputError.jsx'
import { Transition } from '@headlessui/react'
import { dataObject } from '@/Pages/User/helper.js'
import { useEffect, useState } from 'react'
import { store, update } from '@actions/UserController.js'
import usePermissions from '@/Hooks/usePermissions.js'
import { permissions } from '@/Utils/permissions/index.js'

export default function Form({ getUsers, user = null, roles }) {
    const { can } = usePermissions()

    const [action, setAction] = useState(store.route())
    const { data, setData, post, errors, processing, recentlySuccessful, reset } = useForm(dataObject(null))
    const [showSaveButton, setShowSaveButton] = useState(false)

    useEffect(() => {
        setAction(user ? update.route({ user: user.id }) : store.route())
        setData(dataObject(user))
        setShowSaveButton(user ? can(permissions.user.update) : can(permissions.user.create))
    }, [user])

    const submit = (e) => {
        e.preventDefault()

        post(action, {
            onSuccess: (r) => {
                if (!user) {
                    reset('name', 'email', 'password', 'role')
                }

                getUsers()
            },
            onError: () => {},
        })
    }

    return (
        <form onSubmit={submit}>
            <div className="card mb-6 w-2/3">
                <div className="card-header">
                    <h5 className="card-title m-0 text-lg">User Details</h5>
                </div>
                <div className="card-body">
                    <div className="row g-5">
                        <div className="col-12 col-md-12">
                            <div className="form-floating form-floating-outline">
                                <TextInput
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    id="user-name"
                                    placeholder="Name"
                                    required={true}
                                    isFocused={true}
                                />
                                <InputLabel htmlFor="user-name" required={true}>
                                    Name
                                </InputLabel>
                                <InputError className="mt-2" message={errors.name} />
                            </div>
                        </div>
                        <div className="col-12 col-md-6">
                            <div className="form-floating form-floating-outline">
                                <TextInput
                                    type="text"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    id="user-email"
                                    placeholder="Email"
                                    required={true}
                                />
                                <InputLabel htmlFor="user-email" required={true}>
                                    Email
                                </InputLabel>
                                <InputError className="mt-2" message={errors.email} />
                            </div>
                        </div>
                        <div className="col-12 col-md-6">
                            <div className="form-floating form-floating-outline">
                                <TextInput
                                    type="password"
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    id="user-password"
                                    placeholder="Password"
                                    required={!user}
                                />
                                <InputLabel htmlFor="user-password" required={true}>
                                    Password
                                </InputLabel>
                                <InputError className="mt-2" message={errors.password} />
                                {user && (
                                    <small className="text-muted">
                                        If you enter a password, the current password will be replaced.
                                    </small>
                                )}
                            </div>
                        </div>
                        <div className="col-12 col-md-12">
                            <div className="form-floating form-floating-outline">
                                <select
                                    id="role"
                                    className="select2 form-select"
                                    data-placeholder="Role"
                                    value={data.role}
                                    onChange={(e) => setData('role', e.target.value)}
                                >
                                    <option value="">Select Role</option>
                                    {roles.map((role) => (
                                        <option key={role.id} value={role.id}>
                                            {role.display_name}
                                        </option>
                                    ))}
                                </select>
                                <InputLabel htmlFor="role" required={true}>
                                    Role
                                </InputLabel>
                                <InputError className="mt-2" message={errors.role} />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {showSaveButton && (
                <div className="d-flex justify-content-end w-2/3 gap-4">
                    <button disabled={processing} className="btn btn-primary">
                        Save Changes
                    </button>
                    <Transition
                        show={recentlySuccessful}
                        enter="transition ease-in-out"
                        enterFrom="opacity-0"
                        leave="transition ease-in-out"
                        leaveTo="opacity-0"
                    >
                        <p className="mt-3 text-sm text-gray-600">Saved.</p>
                    </Transition>
                </div>
            )}
        </form>
    )
}
