import TopHeader from '@/Components/layout/TopHeader.jsx'
import TopMenu from '@/Components/layout/TopMenu.jsx'
import Footer from '@/Components/layout/Footer.jsx'
import { usePage } from '@inertiajs/react'

export default function Master({ children }) {
    const { auth } = usePage().props

    return (
        <div className="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
            <div className="layout-container">
                <TopHeader user={auth.user} />

                <div className="layout-page">
                    <div className="content-wrapper">
                        <TopMenu />

                        <div className="container-xxl flex-grow-1 container-p-y">{children}</div>

                        <Footer />
                        <div className="content-backdrop fade"></div>
                    </div>
                </div>
            </div>
        </div>
    )
}
