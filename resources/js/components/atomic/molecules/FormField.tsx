import React, { memo, useId } from 'react';
import InputField from '../atoms/InputField';
import SelectField from '../atoms/SelectField';
import TextAreaField from '../atoms/TextAreaField';
import CheckboxField from '../atoms/CheckboxField';
import type { FormFieldProps } from '@/types/componentes';

const FormField = memo<FormFieldProps>((props) => {
    const generatedId = useId();
    const fieldId = props.name || generatedId;

    // Common props for all field types
    const commonProps = {
        id: fieldId,
        error: props.error,
        helperText: props.helperText,
        required: props.required,
        disabled: props.disabled,
        className: props.className
    };

    switch (props.type) {
        case 'input':
            return (
                <div className="space-y-1">
                    {props.label && (
                        <label
                            htmlFor={fieldId}
                            className="block text-sm font-medium text-gray-700"
                        >
                            {props.label}
                            {props.required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                    )}
                    <InputField
                        {...commonProps}
                        inputType={props.inputType}
                        value={props.value}
                        onChange={props.onChange}
                        placeholder={props.placeholder}
                    />
                </div>
            );

        case 'select':
            return (
                <div className="space-y-1">
                    {props.label && (
                        <label
                            htmlFor={fieldId}
                            className="block text-sm font-medium text-gray-700"
                        >
                            {props.label}
                            {props.required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                    )}
                    <SelectField
                        {...commonProps}
                        options={props.options || []}
                        value={props.value}
                        onChange={props.onChange}
                        placeholder={props.placeholder}
                    />
                </div>
            );

        case 'textarea':
            return (
                <div className="space-y-1">
                    {props.label && (
                        <label
                            htmlFor={fieldId}
                            className="block text-sm font-medium text-gray-700"
                        >
                            {props.label}
                            {props.required && <span className="text-red-500 ml-1">*</span>}
                        </label>
                    )}
                    <TextAreaField
                        {...commonProps}
                        value={props.value}
                        onChange={props.onChange}
                        placeholder={props.placeholder}
                        rows={props.rows}
                        showCounter={props.showCounter}
                        maxLength={props.maxLength}
                    />
                </div>
            );

        case 'checkbox':
            return (
                <CheckboxField
                    {...commonProps}
                    label={props.label || ''}
                    checked={props.checked}
                    onChange={props.onChange}
                    indeterminate={props.indeterminate}
                    labelPosition={props.labelPosition}
                />
            );

        default:
            return null;
    }
});

FormField.displayName = 'FormField';

export default FormField;
