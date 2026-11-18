import imageLogo from "@/assets/circle-logo.png";

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-12 shrink-0 items-center justify-center rounded-md group-data-[collapsible=icon]:size-10">
                <img src={imageLogo} alt="" className="size-10 object-contain group-data-[collapsible=icon]:size-8" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm group-data-[collapsible=icon]:hidden">
                <span className="mb-0.5 truncate leading-tight font-semibold text-cajas-text-active">
                    Comfaca en línea
                </span>
            </div>
        </>
    );
}
