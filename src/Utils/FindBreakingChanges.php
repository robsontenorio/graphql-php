<?php
/**
 * Utility for finding breaking/dangerous changes between two schemas.
 */

namespace GraphQL\Utils;


class FindBreakingChanges {

  const BREAKING_CHANGE_FIELD_CHANGED = 'FIELD_CHANGED_KIND';
  const BREAKING_CHANGE_FIELD_REMOVED = 'FIELD_REMOVED';
  const BREAKING_CHANGE_TYPE_CHANGED = 'TYPE_CHANGED_KIND';
  const BREAKING_CHANGE_TYPE_REMOVED = 'TYPE_REMOVED';
  const BREAKING_CHANGE_TYPE_REMOVED_FROM_UNION = 'TYPE_REMOVED_FROM_UNION';
  const BREAKING_CHANGE_VALUE_REMOVED_FROM_ENUM = 'VALUE_REMOVED_FROM_ENUM';
  const BREAKING_CHANGE_ARG_REMOVED = 'ARG_REMOVED';
  const BREAKING_CHANGE_ARG_CHANGED = 'ARG_CHANGED_KIND';
  const BREAKING_CHANGE_NON_NULL_ARG_ADDED = 'NON_NULL_ARG_ADDED';
  const BREAKING_CHANGE_NON_NULL_INPUT_FIELD_ADDED = 'NON_NULL_INPUT_FIELD_ADDED';
  const BREAKING_CHANGE_INTERFACE_REMOVED_FROM_OBJECT = 'INTERFACE_REMOVED_FROM_OBJECT';
  /*
  export const DangerousChangeType = {
    ARG_DEFAULT_VALUE_CHANGE: 'ARG_DEFAULT_VALUE_CHANGE',
    VALUE_ADDED_TO_ENUM: 'VALUE_ADDED_TO_ENUM',
    TYPE_ADDED_TO_UNION: 'TYPE_ADDED_TO_UNION',
  };*/

  /**
   * Given two schemas, returns an Array containing descriptions of all the types
   * of breaking changes covered by the other functions down below.
   *
   * @return array
   */
  public function findBreakingChanges(
      $oldSchema, $newSchema
  ) {
    return [
      /*...findRemovedTypes(oldSchema, newSchema),
      ...findTypesThatChangedKind(oldSchema, newSchema),
      ...findFieldsThatChangedType(oldSchema, newSchema),
      ...findTypesRemovedFromUnions(oldSchema, newSchema),
      ...findValuesRemovedFromEnums(oldSchema, newSchema),
      ...findArgChanges(oldSchema, newSchema).breakingChanges,
      ...findInterfacesRemovedFromObjectTypes(oldSchema, newSchema),
   */
    ];
  }

  /**
   * Given two schemas, returns an Array containing descriptions of all the types
   * of potentially dangerous changes covered by the other functions down below.
   *
   * @return array
   */
  public function findDangerousChanges(
      $oldSchema, $newSchema
  ) {
    return [
      /*   ...findArgChanges(oldSchema, newSchema).dangerousChanges,
         ...findValuesAddedToEnums(oldSchema, newSchema),
         ...findTypesAddedToUnions(oldSchema, newSchema)
       */
    ];
  }

  /**
   * Given two schemas, returns an Array containing descriptions of any breaking
   * changes in the newSchema related to removing an entire type.
   */
  public function findRemovedTypes(
      $oldSchema, $newSchema
  ) {
    /*const oldTypeMap = oldSchema.getTypeMap();
    const newTypeMap = newSchema.getTypeMap();

    const breakingChanges = [];
    Object.keys(oldTypeMap).forEach(typeName => {
      if (!newTypeMap[typeName]) {
        breakingChanges.push({
          type: BreakingChangeType.TYPE_REMOVED,
          description: `${typeName} was removed.`,
        });
      }
    });
    return breakingChanges;*/
  }

  /**
   * Given two schemas, returns an Array containing descriptions of any breaking
   * changes in the newSchema related to changing the type of a type.
   */
  public function findTypesThatChangedKind(
      $oldSchema, $newSchema
  ) {
    /*const oldTypeMap = oldSchema.getTypeMap();
    const newTypeMap = newSchema.getTypeMap();

    const breakingChanges = [];
    Object.keys(oldTypeMap).forEach(typeName => {
      if (!newTypeMap[typeName]) {
        return;
      }
      const oldType = oldTypeMap[typeName];
      const newType = newTypeMap[typeName];
      if (!(oldType instanceof newType.constructor)) {
        breakingChanges.push({
          type: BreakingChangeType.TYPE_CHANGED_KIND,
          description: `${typeName} changed from ` +
                       `${typeKindName(oldType)} to ${typeKindName(newType)}.`
        });
      }
    });
    return breakingChanges;
  */
  }

  /**
   * Given two schemas, returns an Array containing descriptions of any
   * breaking or dangerous changes in the newSchema related to arguments
   * (such as removal or change of type of an argument, or a change in an
   * argument's default value).
   */
  public function findArgChanges(
      $oldSchema, $newSchema
  ) {
    /* const oldTypeMap = oldSchema.getTypeMap();
     const newTypeMap = newSchema.getTypeMap();

     const breakingChanges = [];
     const dangerousChanges = [];

     Object.keys(oldTypeMap).forEach(typeName => {
       const oldType = oldTypeMap[typeName];
       const newType = newTypeMap[typeName];
       if (
           !(oldType instanceof GraphQLObjectType ||
             oldType instanceof GraphQLInterfaceType) ||
           !(newType instanceof oldType.constructor)
       ) {
         return;
       }

       const oldTypeFields: GraphQLFieldMap<*, *> = oldType.getFields();
       const newTypeFields: GraphQLFieldMap<*, *> = newType.getFields();

       Object.keys(oldTypeFields).forEach(fieldName => {
         if (!newTypeFields[fieldName]) {
           return;
         }

         oldTypeFields[fieldName].args.forEach(oldArgDef => {
           const newArgs = newTypeFields[fieldName].args;
           const newArgDef = newArgs.find(
                   arg => arg.name === oldArgDef.name
           );

           // Arg not present
           if (!newArgDef) {
             breakingChanges.push({
               type: BreakingChangeType.ARG_REMOVED,
               description: `${oldType.name}.${fieldName} arg ` +
                            `${oldArgDef.name} was removed`,
             });
           } else {
             const isSafe = isChangeSafeForInputObjectFieldOrFieldArg(
                 oldArgDef.type,
                 newArgDef.type,
             );
             if (!isSafe) {
               breakingChanges.push({
                 type: BreakingChangeType.ARG_CHANGED_KIND,
                 description: `${oldType.name}.${fieldName} arg ` +
                              `${oldArgDef.name} has changed type from ` +
                              `${oldArgDef.type.toString()} to ${newArgDef.type.toString()}`,
               });
             } else if (oldArgDef.defaultValue !== undefined &&
                        oldArgDef.defaultValue !== newArgDef.defaultValue) {
               dangerousChanges.push({
                 type: DangerousChangeType.ARG_DEFAULT_VALUE_CHANGE,
                 description: `${oldType.name}.${fieldName} arg ` +
                              `${oldArgDef.name} has changed defaultValue`,
               });
             }
           }
         });
         // Check if a non-null arg was added to the field
         newTypeFields[fieldName].args.forEach(newArgDef => {
           const oldArgs = oldTypeFields[fieldName].args;
           const oldArgDef = oldArgs.find(
                   arg => arg.name === newArgDef.name
           );
           if (!oldArgDef && newArgDef.type instanceof GraphQLNonNull) {
             breakingChanges.push({
               type: BreakingChangeType.NON_NULL_ARG_ADDED,
               description: `A non-null arg ${newArgDef.name} on ` +
                            `${newType.name}.${fieldName} was added`,
             });
           }
         });
       });
     });

     return {
       breakingChanges,
       dangerousChanges,
     };*/
  }

  private static function typeKindName($type) {
    /*  if (type instanceof GraphQLScalarType) {
        return 'a Scalar type';
      }
      if (type instanceof GraphQLObjectType) {
        return 'an Object type';
      }
      if (type instanceof GraphQLInterfaceType) {
        return 'an Interface type';
      }
      if (type instanceof GraphQLUnionType) {
        return 'a Union type';
      }
      if (type instanceof GraphQLEnumType) {
        return 'an Enum type';
      }
      if (type instanceof GraphQLInputObjectType) {
        return 'an Input type';
      }
      throw new TypeError('Unknown type ' + type.constructor.name);*/
  }

  /**
   * Given two schemas, returns an Array containing descriptions of any breaking
   * changes in the newSchema related to the fields on a type. This includes if
   * a field has been removed from a type, if a field has changed type, or if
   * a non-null field is added to an input type.
   */
  public static function findFieldsThatChangedType(
      $oldSchema, $newSchema
  ) {
    /*return [
      ...findFieldsThatChangedTypeOnObjectOrInterfaceTypes(oldSchema, newSchema),
      ...findFieldsThatChangedTypeOnInputObjectTypes(oldSchema, newSchema),
    ];*/
  }

  private static function findFieldsThatChangedTypeOnObjectOrInterfaceTypes(
      $oldSchema, $newSchema
  ) {
    /*const oldTypeMap = oldSchema.getTypeMap();
    const newTypeMap = newSchema.getTypeMap();

    const breakingFieldChanges = [];
    Object.keys(oldTypeMap).forEach(typeName => {
      const oldType = oldTypeMap[typeName];
      const newType = newTypeMap[typeName];
      if (
          !(oldType instanceof GraphQLObjectType ||
            oldType instanceof GraphQLInterfaceType) ||
          !(newType instanceof oldType.constructor)
      ) {
        return;
      }

      const oldTypeFieldsDef = oldType.getFields();
      const newTypeFieldsDef = newType.getFields();
      Object.keys(oldTypeFieldsDef).forEach(fieldName => {
        // Check if the field is missing on the type in the new schema.
        if (!(fieldName in newTypeFieldsDef)) {
          breakingFieldChanges.push({
            type: BreakingChangeType.FIELD_REMOVED,
            description: `${typeName}.${fieldName} was removed.`,
          });
        } else {
          const oldFieldType = oldTypeFieldsDef[fieldName].type;
          const newFieldType = newTypeFieldsDef[fieldName].type;
          const isSafe =
          isChangeSafeForObjectOrInterfaceField(oldFieldType, newFieldType);
          if (!isSafe) {
            const oldFieldTypeString = isNamedType(oldFieldType) ?
                oldFieldType.name :
                oldFieldType.toString();
            const newFieldTypeString = isNamedType(newFieldType) ?
                newFieldType.name :
                newFieldType.toString();
            breakingFieldChanges.push({
              type: BreakingChangeType.FIELD_CHANGED_KIND,
              description: `${typeName}.${fieldName} changed type from ` +
                           `${oldFieldTypeString} to ${newFieldTypeString}.`,
            });
          }
        }
      });
    });
    return breakingFieldChanges;*/
  }

  public static function findFieldsThatChangedTypeOnInputObjectTypes(
      $oldSchema, $newSchema
  ) {
    /*  const oldTypeMap = oldSchema.getTypeMap();
      const newTypeMap = newSchema.getTypeMap();

      const breakingFieldChanges = [];
      Object.keys(oldTypeMap).forEach(typeName => {
        const oldType = oldTypeMap[typeName];
        const newType = newTypeMap[typeName];
        if (
            !(oldType instanceof GraphQLInputObjectType) ||
            !(newType instanceof GraphQLInputObjectType)
        ) {
          return;
        }

        const oldTypeFieldsDef = oldType.getFields();
        const newTypeFieldsDef = newType.getFields();
        Object.keys(oldTypeFieldsDef).forEach(fieldName => {
          // Check if the field is missing on the type in the new schema.
          if (!(fieldName in newTypeFieldsDef)) {
            breakingFieldChanges.push({
              type: BreakingChangeType.FIELD_REMOVED,
              description: `${typeName}.${fieldName} was removed.`,
            });
          } else {
            const oldFieldType = oldTypeFieldsDef[fieldName].type;
            const newFieldType = newTypeFieldsDef[fieldName].type;

            const isSafe =
            isChangeSafeForInputObjectFieldOrFieldArg(oldFieldType, newFieldType);
            if (!isSafe) {
              const oldFieldTypeString = isNamedType(oldFieldType) ?
                  oldFieldType.name :
                  oldFieldType.toString();
              const newFieldTypeString = isNamedType(newFieldType) ?
                  newFieldType.name :
                  newFieldType.toString();
              breakingFieldChanges.push({
                type: BreakingChangeType.FIELD_CHANGED_KIND,
                description: `${typeName}.${fieldName} changed type from ` +
                             `${oldFieldTypeString} to ${newFieldTypeString}.`,
              });
            }
          }
        });
        // Check if a non-null field was added to the input object type
        Object.keys(newTypeFieldsDef).forEach(fieldName => {
          if (
          !(fieldName in oldTypeFieldsDef) &&
            newTypeFieldsDef[fieldName].type instanceof GraphQLNonNull
          ) {
            breakingFieldChanges.push({
              type: BreakingChangeType.NON_NULL_INPUT_FIELD_ADDED,
              description: `A non-null field ${fieldName} on ` +
                           `input type ${newType.name} was added.`,
            });
          }
        });
      });
      return breakingFieldChanges;*/
  }

  private static function isChangeSafeForObjectOrInterfaceField(
      $oldType, $newType
  ) {
    /*if (isNamedType(oldType)) {
      return (
               // if they're both named types, see if their names are equivalent
                 isNamedType(newType) && oldType.name === newType.name
             ) ||
             (
               // moving from nullable to non-null of the same underlying type is safe
                 newType instanceof GraphQLNonNull &&
                 isChangeSafeForObjectOrInterfaceField(
                     oldType,
                     newType.ofType,
          )
             );
    } else if (oldType instanceof GraphQLList) {
      return (
               // if they're both lists, make sure the underlying types are compatible
                 newType instanceof GraphQLList &&
                 isChangeSafeForObjectOrInterfaceField(
                     oldType.ofType,
                     newType.ofType,
          )
             ) ||
             (
               // moving from nullable to non-null of the same underlying type is safe
                 newType instanceof GraphQLNonNull &&
                 isChangeSafeForObjectOrInterfaceField(
                     oldType,
                     newType.ofType,
          )
             );
    } else if (oldType instanceof GraphQLNonNull) {
      // if they're both non-null, make sure the underlying types are compatible
      return newType instanceof GraphQLNonNull &&
             isChangeSafeForObjectOrInterfaceField(
                 oldType.ofType,
                 newType.ofType,
        );
    }
    return false;*/
  }

  private static function isChangeSafeForInputObjectFieldOrFieldArg(
      $oldType, $newType
  ) {
    /* if (isNamedType(oldType)) {
       // if they're both named types, see if their names are equivalent
       return isNamedType(newType) && oldType.name === newType.name;
     } else if (oldType instanceof GraphQLList) {
       // if they're both lists, make sure the underlying types are compatible
       return newType instanceof GraphQLList &&
              isChangeSafeForInputObjectFieldOrFieldArg(
                  oldType.ofType,
                  newType.ofType,
         );
     } else if (oldType instanceof GraphQLNonNull) {
       return (
                // if they're both non-null, make sure the underlying types are
                // compatible
                  newType instanceof GraphQLNonNull &&
                  isChangeSafeForInputObjectFieldOrFieldArg(
                      oldType.ofType,
                      newType.ofType,
           )
              ) ||
              (
                // moving from non-null to nullable of the same underlying type is safe
                  !(newType instanceof GraphQLNonNull) &&
                  isChangeSafeForInputObjectFieldOrFieldArg(
                      oldType.ofType,
                      newType,
           )
              );
     }
     return false;*/
  }

  /**
   * Given two schemas, returns an Array containing descriptions of any breaking
   * changes in the newSchema related to removing types from a union type.
   */
  public static function findTypesRemovedFromUnions(
      $oldSchema, $newSchema
  ) {
    /*  const oldTypeMap = oldSchema.getTypeMap();
      const newTypeMap = newSchema.getTypeMap();

      const typesRemovedFromUnion = [];
      Object.keys(oldTypeMap).forEach(typeName => {
        const oldType = oldTypeMap[typeName];
        const newType = newTypeMap[typeName];
        if (!(oldType instanceof GraphQLUnionType) ||
            !(newType instanceof GraphQLUnionType)) {
          return;
        }
        const typeNamesInNewUnion = Object.create(null);
        newType.getTypes().forEach(type => {
          typeNamesInNewUnion[type.name] = true;
        });
        oldType.getTypes().forEach(type => {
          if (!typeNamesInNewUnion[type.name]) {
            typesRemovedFromUnion.push({
              type: BreakingChangeType.TYPE_REMOVED_FROM_UNION,
              description: `${type.name} was removed from union type ${typeName}.`
            });
          }
        });
      });
      return typesRemovedFromUnion;*/
  }

  /**
   * Given two schemas, returns an Array containing descriptions of any dangerous
   * changes in the newSchema related to adding types to a union type.
   */
  public static function findTypesAddedToUnions(
      $oldSchema, $newSchema
  ) {
    /* const oldTypeMap = oldSchema.getTypeMap();
     const newTypeMap = newSchema.getTypeMap();

     const typesAddedToUnion = [];
     Object.keys(newTypeMap).forEach(typeName => {
       const oldType = oldTypeMap[typeName];
       const newType = newTypeMap[typeName];
       if (!(oldType instanceof GraphQLUnionType) ||
           !(newType instanceof GraphQLUnionType)) {
         return;
       }
       const typeNamesInOldUnion = Object.create(null);
       oldType.getTypes().forEach(type => {
         typeNamesInOldUnion[type.name] = true;
       });
       newType.getTypes().forEach(type => {
         if (!typeNamesInOldUnion[type.name]) {
           typesAddedToUnion.push({
             type: DangerousChangeType.TYPE_ADDED_TO_UNION,
             description: `${type.name} was added to union type ${typeName}.`
           });
         }
       });
     });
     return typesAddedToUnion;*/
  }

  /**
   * Given two schemas, returns an Array containing descriptions of any breaking
   * changes in the newSchema related to removing values from an enum type.
   */
  public static function findValuesRemovedFromEnums(
      $oldSchema, $newSchema
  ) {
    /* const oldTypeMap = oldSchema.getTypeMap();
     const newTypeMap = newSchema.getTypeMap();

     const valuesRemovedFromEnums = [];
     Object.keys(oldTypeMap).forEach(typeName => {
       const oldType = oldTypeMap[typeName];
       const newType = newTypeMap[typeName];
       if (!(oldType instanceof GraphQLEnumType) ||
           !(newType instanceof GraphQLEnumType)) {
         return;
       }
       const valuesInNewEnum = Object.create(null);
       newType.getValues().forEach(value => {
         valuesInNewEnum[value.name] = true;
       });
       oldType.getValues().forEach(value => {
         if (!valuesInNewEnum[value.name]) {
           valuesRemovedFromEnums.push({
             type: BreakingChangeType.VALUE_REMOVED_FROM_ENUM,
             description: `${value.name} was removed from enum type ${typeName}.`
           });
         }
       });
     });
     return valuesRemovedFromEnums;*/
  }

  /**
   * Given two schemas, returns an Array containing descriptions of any dangerous
   * changes in the newSchema related to adding values to an enum type.
   */
  public static function findValuesAddedToEnums(
      $oldSchema, $newSchema
  ) {
    /* const oldTypeMap = oldSchema.getTypeMap();
     const newTypeMap = newSchema.getTypeMap();

     const valuesAddedToEnums = [];
     Object.keys(oldTypeMap).forEach(typeName => {
       const oldType = oldTypeMap[typeName];
       const newType = newTypeMap[typeName];
       if (!(oldType instanceof GraphQLEnumType) ||
           !(newType instanceof GraphQLEnumType)) {
         return;
       }

       const valuesInOldEnum = Object.create(null);
       oldType.getValues().forEach(value => {
         valuesInOldEnum[value.name] = true;
       });
       newType.getValues().forEach(value => {
         if (!valuesInOldEnum[value.name]) {
           valuesAddedToEnums.push({
             type: DangerousChangeType.VALUE_ADDED_TO_ENUM,
             description: `${value.name} was added to enum type ${typeName}.`
           });
         }
       });
     });
     return valuesAddedToEnums;*/
  }

  public static function findInterfacesRemovedFromObjectTypes(
      $oldSchema, $newSchema
  ) {
    /* const oldTypeMap = oldSchema.getTypeMap();
     const newTypeMap = newSchema.getTypeMap();
     const breakingChanges = [];

     Object.keys(oldTypeMap).forEach(typeName => {
       const oldType = oldTypeMap[typeName];
       const newType = newTypeMap[typeName];
       if (
           !(oldType instanceof GraphQLObjectType) ||
           !(newType instanceof GraphQLObjectType)
       ) {
         return;
       }

       const oldInterfaces = oldType.getInterfaces();
       const newInterfaces = newType.getInterfaces();
       oldInterfaces.forEach(oldInterface => {
         if (!newInterfaces.some(int => int.name === oldInterface.name)) {
           breakingChanges.push({
             type: BreakingChangeType.INTERFACE_REMOVED_FROM_OBJECT,
             description: `${typeName} no longer implements interface ` +
                          `${oldInterface.name}.`
           });
         }
       });
     });
     return breakingChanges;*/
  }
}