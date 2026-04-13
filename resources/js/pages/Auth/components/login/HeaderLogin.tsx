import type React from "react"
import { ChevronLeft } from "lucide-react"
import UserTypeDescription from "@/components/auth/user-type-description"

interface HeaderLoginProps {
  userTypes: { id: string; label: string }[]
  selectedUserType: string | null
  onBack: () => void
}

const HeaderLogin: React.FC<HeaderLoginProps> = ({
  userTypes,
  selectedUserType,
  onBack,
}) => {
  return (
    <div className="flex items-center mb-6">
      <button onClick={onBack} className="mr-3 p-2 hover:bg-gray-100 rounded-full transition-colors">
        <ChevronLeft className="w-5 h-5 text-gray-600" />
      </button>
      <div>
        <h2 className="text-xl font-semibold text-gray-800">
          {userTypes.find((ut) => ut.id === selectedUserType)?.label}
        </h2>
        <p className="text-sm text-gray-600">Ingresa tus credenciales</p>
        {selectedUserType && (
          <UserTypeDescription userTypeId={selectedUserType} />
        )}
      </div>
    </div>
  )
}

export default HeaderLogin
