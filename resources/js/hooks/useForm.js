import { useState, useCallback } from 'react';

export const useForm = (initialValues = {}, validationRules = {}) => {
    const [values, setValues] = useState(initialValues);
    const [errors, setErrors] = useState({});
    const [touched, setTouched] = useState({});
    const [isSubmitting, setIsSubmitting] = useState(false);

    const setValue = useCallback((name, value) => {
        setValues(prev => ({ ...prev, [name]: value }));
        
        // Clear error when user starts typing
        if (errors[name]) {
            setErrors(prev => ({ ...prev, [name]: '' }));
        }
    }, [errors]);

    const setFieldTouched = useCallback((name, isTouched = true) => {
        setTouched(prev => ({ ...prev, [name]: isTouched }));
    }, []);

    const handleChange = useCallback((e) => {
        const { name, value, type, checked } = e.target;
        const fieldValue = type === 'checkbox' ? checked : value;
        setValue(name, fieldValue);
    }, [setValue]);

    const handleBlur = useCallback((e) => {
        const { name } = e.target;
        setFieldTouched(name, true);
        
        // Validate field on blur if validation rules exist
        if (validationRules[name]) {
            validateField(name, values[name]);
        }
    }, [values, validationRules]);

    const validateField = useCallback((name, value) => {
        const rules = validationRules[name];
        if (!rules) return '';

        for (const rule of rules) {
            const error = rule(value, values);
            if (error) {
                setErrors(prev => ({ ...prev, [name]: error }));
                return error;
            }
        }

        setErrors(prev => ({ ...prev, [name]: '' }));
        return '';
    }, [values, validationRules]);

    const validateForm = useCallback(() => {
        const newErrors = {};
        let isValid = true;

        Object.keys(validationRules).forEach(name => {
            const error = validateField(name, values[name]);
            if (error) {
                newErrors[name] = error;
                isValid = false;
            }
        });

        setErrors(newErrors);
        return isValid;
    }, [values, validationRules, validateField]);

    const handleSubmit = useCallback((onSubmit) => async (e) => {
        e.preventDefault();
        setIsSubmitting(true);

        // Mark all fields as touched
        const allTouched = Object.keys(values).reduce((acc, key) => {
            acc[key] = true;
            return acc;
        }, {});
        setTouched(allTouched);

        // Validate form
        const isValid = validateForm();
        
        if (isValid && onSubmit) {
            try {
                await onSubmit(values);
            } catch (error) {
                console.error('Form submission error:', error);
            }
        }

        setIsSubmitting(false);
    }, [values, validateForm]);

    const reset = useCallback((newValues = initialValues) => {
        setValues(newValues);
        setErrors({});
        setTouched({});
        setIsSubmitting(false);
    }, [initialValues]);

    const setFieldError = useCallback((name, error) => {
        setErrors(prev => ({ ...prev, [name]: error }));
    }, []);

    const setFormErrors = useCallback((newErrors) => {
        setErrors(newErrors);
    }, []);

    return {
        values,
        errors,
        touched,
        isSubmitting,
        handleChange,
        handleBlur,
        handleSubmit,
        setValue,
        setFieldTouched,
        setFieldError,
        setFormErrors,
        validateForm,
        reset,
        isValid: Object.keys(errors).length === 0
    };
};