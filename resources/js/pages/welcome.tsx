import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Hotel Management System">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
            </Head>
            <div className="flex min-h-screen flex-col items-center bg-gradient-to-br from-blue-50 to-indigo-100 p-6 text-gray-900">
                <header className="mb-6 w-full max-w-4xl">
                    <nav className="flex items-center justify-end gap-4">
                        {auth.user ? (
                            <Link
                                href={route('hotel-dashboard.index')}
                                className="inline-block rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-blue-700 transition-colors"
                            >
                                Go to Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="inline-block rounded-lg border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                                >
                                    Staff Login
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="inline-block rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors"
                                >
                                    Register
                                </Link>
                            </>
                        )}
                    </nav>
                </header>

                <div className="flex w-full max-w-6xl items-center justify-center">
                    <main className="w-full">
                        {/* Hero Section */}
                        <div className="text-center mb-16">
                            <div className="text-6xl mb-6">🏨</div>
                            <h1 className="text-5xl font-bold text-gray-900 mb-6">
                                Hotel Management System
                            </h1>
                            <p className="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                                Comprehensive hotel operations platform for internal staff. 
                                Streamline reservations, room management, guest services, and housekeeping operations.
                            </p>
                            {!auth.user && (
                                <div className="flex gap-4 justify-center">
                                    <Link
                                        href={route('login')}
                                        className="inline-block rounded-lg bg-blue-600 px-8 py-3 text-lg font-semibold text-white shadow-lg hover:bg-blue-700 transition-colors"
                                    >
                                        Staff Login
                                    </Link>
                                    <Link
                                        href={route('register')}
                                        className="inline-block rounded-lg border border-gray-300 px-8 py-3 text-lg font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                                    >
                                        Register Account
                                    </Link>
                                </div>
                            )}
                        </div>

                        {/* Features Grid */}
                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                            <div className="bg-white rounded-xl p-6 shadow-lg">
                                <div className="text-3xl mb-4">📅</div>
                                <h3 className="text-xl font-semibold mb-3">Reservation Management</h3>
                                <ul className="text-gray-600 space-y-2">
                                    <li>• Create, modify, and cancel reservations</li>
                                    <li>• Real-time availability checking</li>
                                    <li>• Room assignment and upgrades</li>
                                    <li>• Guest preference tracking</li>
                                </ul>
                            </div>

                            <div className="bg-white rounded-xl p-6 shadow-lg">
                                <div className="text-3xl mb-4">🛏️</div>
                                <h3 className="text-xl font-semibold mb-3">Room Management</h3>
                                <ul className="text-gray-600 space-y-2">
                                    <li>• Room type and pricing control</li>
                                    <li>• Status tracking and updates</li>
                                    <li>• Amenities and features management</li>
                                    <li>• Maintenance scheduling</li>
                                </ul>
                            </div>

                            <div className="bg-white rounded-xl p-6 shadow-lg">
                                <div className="text-3xl mb-4">👥</div>
                                <h3 className="text-xl font-semibold mb-3">Guest Management</h3>
                                <ul className="text-gray-600 space-y-2">
                                    <li>• Complete guest profiles</li>
                                    <li>• Stay history and preferences</li>
                                    <li>• Special requests tracking</li>
                                    <li>• Contact information management</li>
                                </ul>
                            </div>

                            <div className="bg-white rounded-xl p-6 shadow-lg">
                                <div className="text-3xl mb-4">💰</div>
                                <h3 className="text-xl font-semibold mb-3">Billing & Payments</h3>
                                <ul className="text-gray-600 space-y-2">
                                    <li>• Automated billing calculations</li>
                                    <li>• Payment tracking and processing</li>
                                    <li>• Invoice generation</li>
                                    <li>• Financial reporting</li>
                                </ul>
                            </div>

                            <div className="bg-white rounded-xl p-6 shadow-lg">
                                <div className="text-3xl mb-4">🧹</div>
                                <h3 className="text-xl font-semibold mb-3">Housekeeping</h3>
                                <ul className="text-gray-600 space-y-2">
                                    <li>• Task assignment and tracking</li>
                                    <li>• Room status updates</li>
                                    <li>• Maintenance request management</li>
                                    <li>• Staff scheduling and coordination</li>
                                </ul>
                            </div>

                            <div className="bg-white rounded-xl p-6 shadow-lg">
                                <div className="text-3xl mb-4">📊</div>
                                <h3 className="text-xl font-semibold mb-3">Reporting & Analytics</h3>
                                <ul className="text-gray-600 space-y-2">
                                    <li>• Occupancy and revenue reports</li>
                                    <li>• Performance metrics dashboard</li>
                                    <li>• Guest satisfaction tracking</li>
                                    <li>• Operational insights</li>
                                </ul>
                            </div>
                        </div>

                        {/* Dashboard Preview */}
                        <div className="bg-white rounded-xl p-8 shadow-lg mb-16">
                            <h2 className="text-2xl font-bold text-center mb-8">Staff Dashboard Overview</h2>
                            <div className="grid md:grid-cols-4 gap-6">
                                <div className="text-center p-4 bg-blue-50 rounded-lg">
                                    <div className="text-2xl font-bold text-blue-600">Real-time</div>
                                    <div className="text-gray-600">Room Status</div>
                                </div>
                                <div className="text-center p-4 bg-green-50 rounded-lg">
                                    <div className="text-2xl font-bold text-green-600">Quick</div>
                                    <div className="text-gray-600">Check-in/out</div>
                                </div>
                                <div className="text-center p-4 bg-purple-50 rounded-lg">
                                    <div className="text-2xl font-bold text-purple-600">Instant</div>
                                    <div className="text-gray-600">Availability</div>
                                </div>
                                <div className="text-center p-4 bg-orange-50 rounded-lg">
                                    <div className="text-2xl font-bold text-orange-600">Task</div>
                                    <div className="text-gray-600">Management</div>
                                </div>
                            </div>
                        </div>

                        {/* Call to Action */}
                        <div className="text-center bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-8 text-white">
                            <h2 className="text-3xl font-bold mb-4">Ready to Streamline Your Hotel Operations?</h2>
                            <p className="text-xl mb-6 opacity-90">
                                Join your team and start managing your hotel more efficiently today.
                            </p>
                            {!auth.user && (
                                <div className="flex gap-4 justify-center">
                                    <Link
                                        href={route('register')}
                                        className="inline-block rounded-lg bg-white px-8 py-3 text-lg font-semibold text-blue-600 shadow-lg hover:bg-gray-50 transition-colors"
                                    >
                                        Get Started
                                    </Link>
                                    <Link
                                        href={route('login')}
                                        className="inline-block rounded-lg border border-white px-8 py-3 text-lg font-medium text-white hover:bg-white hover:text-blue-600 transition-colors"
                                    >
                                        Staff Login
                                    </Link>
                                </div>
                            )}
                        </div>

                        <footer className="mt-12 text-center text-gray-500">
                            <p>Professional Hotel Management System</p>
                        </footer>
                    </main>
                </div>
            </div>
        </>
    );
}