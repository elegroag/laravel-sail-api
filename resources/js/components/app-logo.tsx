import imageLogo from "@/assets/circle-logo.png";

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-15 items-center justify-center rounded-md text-sidebar-primary-foreground">
                <div className="size-10 fill-current text-white">
                    <img src={imageLogo} alt="" />
                </div>
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold">Comfaca en línea</span>
            </div>
        </>
    );
}
