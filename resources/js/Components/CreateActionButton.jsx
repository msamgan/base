import usePermissions from '@/Hooks/usePermissions'
import OffCanvasButton from '@/Components/off_canvas/OffCanvasButton.jsx'
import { permissions } from '@/Utils/permissions/index.js'
import { toTitleCase } from '@/Utils/methods.js'

export default function CreateActionButton({ module, onClick }) {
    const { can } = usePermissions()

    return (
        can(permissions[module].create) && (
            <OffCanvasButton onClick={onClick} id={module + 'FormCanvas'}>
                <i className="ri-add-line me-2"></i>
                Create {toTitleCase(module)}
            </OffCanvasButton>
        )
    )
}
