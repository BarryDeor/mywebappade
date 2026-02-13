import React from 'react';
import { cn } from '@/utils/cn';
import { getInitials } from '@/utils/format';

interface AvatarProps {
  src?: string;
  alt?: string;
  firstName?: string;
  lastName?: string;
  size?: 'sm' | 'md' | 'lg' | 'xl';
  className?: string;
}

export const Avatar: React.FC<AvatarProps> = ({
  src,
  alt,
  firstName = '',
  lastName = '',
  size = 'md',
  className,
}) => {
  const sizes = {
    sm: 'w-8 h-8 text-xs',
    md: 'w-10 h-10 text-sm',
    lg: 'w-12 h-12 text-base',
    xl: 'w-16 h-16 text-lg',
  };

  const initials = getInitials(firstName, lastName);

  return (
    <div
      className={cn(
        'rounded-full flex items-center justify-center font-medium',
        sizes[size],
        className
      )}
    >
      {src ? (
        <img src={src} alt={alt || initials} className="w-full h-full rounded-full object-cover" />
      ) : (
        <div className="w-full h-full rounded-full bg-primary-600 text-white flex items-center justify-center">
          {initials}
        </div>
      )}
    </div>
  );
};
