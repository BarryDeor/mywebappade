import React, { useState } from 'react';
import { useAuthStore } from '@/store/authStore';
import { Avatar } from '@/components/common/Avatar';
import { useNavigate } from 'react-router-dom';

export const Header: React.FC = () => {
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();
  const [showDropdown, setShowDropdown] = useState(false);

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <header className="bg-white border-b border-secondary-200 sticky top-0 z-40">
      <div className="px-6 py-4 flex items-center justify-between">
        <div className="flex-1">
          <h2 className="text-xl font-semibold text-secondary-900">
            Welcome back, {user?.firstName}!
          </h2>
          <p className="text-sm text-secondary-600">{user?.role.replace('_', ' ')}</p>
        </div>

        <div className="flex items-center gap-4">
          {/* Notifications */}
          <button className="relative p-2 text-secondary-600 hover:text-secondary-900 hover:bg-secondary-100 rounded-lg transition-colors">
            <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
          </button>

          {/* User Menu */}
          <div className="relative">
            <button
              onClick={() => setShowDropdown(!showDropdown)}
              className="flex items-center gap-3 p-2 hover:bg-secondary-100 rounded-lg transition-colors"
            >
              <Avatar
                firstName={user?.firstName}
                lastName={user?.lastName}
                src={user?.avatar}
                size="md"
              />
              <svg className="w-4 h-4 text-secondary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            {showDropdown && (
              <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-secondary-200 py-1">
                <button
                  onClick={() => {
                    navigate('/profile');
                    setShowDropdown(false);
                  }}
                  className="w-full text-left px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-50"
                >
                  My Profile
                </button>
                <button
                  onClick={() => {
                    navigate('/settings');
                    setShowDropdown(false);
                  }}
                  className="w-full text-left px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-50"
                >
                  Settings
                </button>
                <hr className="my-1 border-secondary-200" />
                <button
                  onClick={handleLogout}
                  className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                >
                  Logout
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </header>
  );
};
