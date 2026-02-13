import { create } from 'zustand';
import { User, AuthState } from '@/types';
import { authService, LoginCredentials } from '@/services/auth.service';

interface AuthStore extends AuthState {
  login: (credentials: LoginCredentials) => Promise<void>;
  logout: () => Promise<void>;
  setUser: (user: User | null) => void;
  initialize: () => void;
}

export const useAuthStore = create<AuthStore>((set) => ({
  user: null,
  token: null,
  isAuthenticated: false,
  isLoading: true,

  initialize: () => {
    const user = authService.getStoredUser();
    const token = authService.getToken();
    set({
      user,
      token,
      isAuthenticated: !!token,
      isLoading: false,
    });
  },

  login: async (credentials: LoginCredentials) => {
    try {
      const { user, token } = await authService.login(credentials);
      set({
        user,
        token,
        isAuthenticated: true,
        isLoading: false,
      });
    } catch (error) {
      set({ isLoading: false });
      throw error;
    }
  },

  logout: async () => {
    try {
      await authService.logout();
    } finally {
      set({
        user: null,
        token: null,
        isAuthenticated: false,
        isLoading: false,
      });
    }
  },

  setUser: (user: User | null) => {
    set({ user });
  },
}));
