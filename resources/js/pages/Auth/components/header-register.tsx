import { ChevronLeft } from "lucide-react"

import type { 
    HeaderRegisterProps 
  } from "@/types/register.d"

const HeaderRegister: React.FC<HeaderRegisterProps> = ({
    subtitle, 
    userTypeLabel, 
    onBack
  }) => {
    return (
      <div className="flex items-center mb-6">
        <button onClick={onBack} className="mr-3 p-2 hover:bg-gray-100 rounded-full transition-colors" type="button">
          <ChevronLeft className="w-5 h-5 text-gray-600" />
        </button>
        <div>
          <h2 className="text-xl font-semibold text-gray-800">{userTypeLabel}</h2>
          <p className="text-sm text-gray-600">{subtitle ?? "Completa tu información"}</p>
        </div>
      </div>
    )
} 

export default HeaderRegister;