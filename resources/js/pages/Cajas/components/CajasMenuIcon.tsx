type CajasMenuIconProps = {
    icon?: string | null;
    color?: string | null;
    className?: string;
};

export function CajasMenuIcon({ icon, color, className = '' }: CajasMenuIconProps) {
    if (!icon) {
        return null;
    }

    const classes = ['cajas-menu-icon', icon, color, className].filter(Boolean).join(' ');

    return <i className={classes} aria-hidden="true" />;
}
