function ToolbeltField(
	{
		type,
		required,
		label,
		setAttributes,
		defaultValue,
		placeholder,
		isSelected
	}
) {

	return (
		<Fragment>
			<div className={'toolbelt-field'}>

				<ToolbeltFieldLabel
					required={required}
					label={label}
					setAttributes={setAttributes}
					isSelected={isSelected}
				/>

				<TextControl
					type={type}
					placeholder={placeholder}
					value={defaultValue}
					onChange={value => setAttributes( { placeholder: value } )}
				/>

			</div>

			<InspectorControls>
				<PanelBody title={__( 'Field Settings', 'wp-toolbelt' )}>

					<TextControl
						label={__( 'Label', 'wp-toolbelt' )}
						value={label}
						onChange={value => setAttributes( { label: value } )}
					/>

					<TextControl
						label={__( 'Default Value', 'wp-toolbelt' )}
						value={defaultValue}
						onChange={value => setAttributes( { defaultValue: value } )}
					/>

					<TextControl
						label={__( 'Placeholder', 'wp-toolbelt' )}
						value={placeholder}
						onChange={value => setAttributes( { placeholder: value } )}
					/>

				</PanelBody>
			</InspectorControls>

		</Fragment>
	);
}