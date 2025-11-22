import { useEffect, useState } from 'react';

const useNotyEmailController = ({ errors }: any) => {
    const [alertMessage, setAlertMessage] = useState<string | null>(null);

    useEffect(() => {
        if (errors?.message) {
            setAlertMessage(errors.message);
        }
    }, [errors]);

    return {
        alertMessage,
    };
};

export default useNotyEmailController;
